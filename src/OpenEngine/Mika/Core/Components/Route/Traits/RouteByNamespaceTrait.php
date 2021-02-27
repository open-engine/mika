<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Traits;

use OpenEngine\Http\Exceptions\NotFoundHttpException;
use OpenEngine\Di\Exceptions\ClassNotFoundException;
use OpenEngine\Di\Exceptions\MethodNotFoundException;
use OpenEngine\Di\Exceptions\MissingMethodArgumentException;
use OpenEngine\Helpers\Path;
use OpenEngine\Mika\Core\Components\Route\Events\AfterCallActionEvent;
use OpenEngine\Mika\Core\Components\Route\Events\BeforeCallActionEvent;
use Psr\Http\Message\ResponseInterface;

trait RouteByNamespaceTrait
{
    use RouteParserTrait;

    /**
     * @var array
     */
    private $routes;

    /**
     * @inheritdoc
     */
    public function getRoutes(): array
    {
        return $this->routes;
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


}
