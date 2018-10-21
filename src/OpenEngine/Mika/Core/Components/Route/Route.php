<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route;

use OpenEngine\Mika\Core\Components\Di\Di;
use OpenEngine\Mika\Core\Components\Di\Exceptions\ClassNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\MethodNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\MissingMethodArgumentException;
use OpenEngine\Mika\Core\Components\Http\Exceptions\NotFoundHttpException;
use OpenEngine\Mika\Core\Components\Route\Interfaces\RouteConfigInterface;
use OpenEngine\Mika\Core\Components\Route\Interfaces\RouteInterface;
use OpenEngine\Mika\Core\Helpers\Path;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Route
 * @package OpenEngine\Mika\Core\Components\Route
 */
class Route implements RouteInterface
{
    /**
     * Default route name
     */
    public const DEFAULT_ROUTE = 'default';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var string
     */
    private $main;

    /**
     * @var string
     */
    private $secondary;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $current;

    /**
     * @var array
     */
    private $routes;

    /**
     * @var ContainerInterface|Di
     */
    private $container;

    /**
     * @inheritdoc
     */
    public function __construct
    (
        RouteConfigInterface $routeConfig,
        RequestInterface $request,
        ContainerInterface $container
    ) {
        $this->routes = $routeConfig->getRoutes();
        $this->request = $request;

        $this->parseUri();
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function getCurrent(): string
    {
        return $this->current;
    }

    /**
     * @inheritdoc
     */
    public function getMain(): string
    {
        return $this->main;
    }

    /**
     * @inheritdoc
     */
    public function getSecondary(): string
    {
        return $this->secondary;
    }

    /**
     * @inheritdoc
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * {@inheritdoc}
     * @throws ClassNotFoundException
     * @throws NotFoundHttpException
     * @throws MethodNotFoundException
     * @throws MissingMethodArgumentException
     */
    public function callControllerAction(): ResponseInterface
    {
        $controller = $this->getController();
        return $this->callAction($controller);
    }

    /**
     * @inheritdoc
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return object
     * @throws ClassNotFoundException
     * @throws MethodNotFoundException
     * @throws MissingMethodArgumentException
     * @throws NotFoundHttpException
     */
    public function getController(): object
    {
        foreach ($this->getRoute($this->getMain()) as $className) {
            $controller = basename(Path::getPathByNamespace($className), 'Controller');

            if ($controller === ucfirst($this->getSecondary())) {
                return $this->container->createObject($className);
            }
        }

        throw new NotFoundHttpException('Controller for ' . $this->getCurrent() . ' is not found');
    }

    /**
     * @inheritdoc
     */
    private function hasRoute(string $name): bool
    {
        return isset($this->routes[$name]);
    }

    /**
     * Get registered as $name route
     *
     * @param string $name
     * @return array
     * @throws NotFoundHttpException Throws this exception when $name route is not registered
     */
    private function getRoute(string $name): array
    {
        if (!$this->hasRoute($name)) {
            throw new NotFoundHttpException('Route ' . $name . ' is not registered');
        }

        return $this->routes[$name];

    }

    private function parseUri(): void
    {
        $parts = explode('/', $this->request->getUri()->getPath());

        $this->main = !empty($parts[1]) && $parts[1] !== '/' ? $parts[1] :  self::DEFAULT_ROUTE;
        $this->secondary = !empty($parts[2]) && $parts[2] !== '/' ? $parts[2] :  self::DEFAULT_ROUTE;
        $this->action = !empty($parts[3]) && $parts[3] !== '/' ? $parts[3] :  self::DEFAULT_ROUTE;

        $this->current =
            '/' . $this->main .
            '/' . $this->secondary .
            '/' . $this->action;
    }

    /**
     * @param object $controller
     * @return ResponseInterface
     * @throws MethodNotFoundException
     * @throws MissingMethodArgumentException
     */
    private function callAction(object $controller): ResponseInterface
    {
        $methodName = $this->getAction() . 'Action';
        // todo user Request method instead of $_GET

        $depends = $this->container->createMethodDepends(\get_class($controller), $methodName, $_GET);
        return \call_user_func_array([$controller, $methodName], $depends);
    }
}
