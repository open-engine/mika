<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route;

use OpenEngine\Di\Di;
use OpenEngine\Di\Exceptions\ClassNotFoundException;
use OpenEngine\Di\Exceptions\MethodNotFoundException;
use OpenEngine\Di\Exceptions\MissingMethodArgumentException;
use OpenEngine\Helpers\Path;
use OpenEngine\Http\Exceptions\NotFoundHttpException;
use OpenEngine\Mika\Core\Components\Route\Events\AfterCallActionEvent;
use OpenEngine\Mika\Core\Components\Route\Events\BeforeCallActionEvent;
use OpenEngine\Mika\Core\Components\Route\Interfaces\RouteConfigInterface;
use OpenEngine\Mika\Core\Components\Route\Interfaces\RouteInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @inheritdoc
     */
    public function __construct(
        RouteConfigInterface $routeConfig,
        RequestInterface $request,
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->routes = $routeConfig->getRoutes();
        $this->request = $request;

        $this->parseUri();
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
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
     * @throws MethodNotFoundException
     * @throws MissingMethodArgumentException
     * @throws NotFoundHttpException
     * @throws ClassNotFoundException
     */
    private function getController(): object
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

        $this->main = $this->getPart($parts, 1);
        $this->secondary = $this->getPart($parts, 2);
        $this->action = $this->getPart($parts, 3);

        $this->current =
            '/' . $this->main .
            '/' . $this->secondary .
            '/' . $this->action;
    }

    private function getPart(array $parts, $key): string
    {
        return !empty($parts[$key]) && $parts[$key] !== '/' ? $parts[$key] : self::DEFAULT_ROUTE;
    }

    /**
     * This method triggers {@see BeforeCallActionEvent} and {@see AfterCallActionEvent} events
     *
     * @param object $controller
     * @return ResponseInterface
     * @throws MethodNotFoundException
     * @throws MissingMethodArgumentException
     * @see BeforeCallActionEvent
     * @see AfterCallActionEvent
     */
    private function callAction(object $controller): ResponseInterface
    {
        $methodName = $this->getAction() . 'Action';
        // todo user Request method instead of $_GET

        $controllerName = \get_class($controller);

        $depends = $this->container->createMethodDepends($controllerName, $methodName, $_GET);

        $this->eventDispatcher->dispatch(new BeforeCallActionEvent($controllerName, $methodName, $depends));

        $response = \call_user_func_array([$controller, $methodName], $depends);

        $event = new AfterCallActionEvent($controllerName, $methodName, $depends, $response);
        $event = $this->eventDispatcher->dispatch($event);

        return $event->getResponse();
    }
}
