<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route;

use OpenEngine\Mika\Core\Components\Route\Interfaces\RestfulRouteInterface;
use OpenEngine\Mika\Core\Components\Route\Interfaces\RouteConfigInterface;
use OpenEngine\Helpers\Path;

/**
 * Class RouteConfig
 * @package OpenEngine\Mika\Core\Components\Route
 */
class RouteConfig implements RouteConfigInterface
{
    /**
     * @var string[]
     */
    private $routes = [];

    /**
     * @var RestfulRouteInterface[]
     */
    private $restfulRoutes = [];

    /**
     * @inheritdoc
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @inheritdoc
     */
    public function register(string $route, string $controllersNamespace): void
    {
        $controllerFiles = \glob(Path::getPathByNamespace($controllersNamespace . '/*'));

        $result = [];

        foreach ($controllerFiles as $controllerFile) {
            $result[] = $controllersNamespace . '\\' . basename($controllerFile, '.php');
        }

        if (empty($result)) {
            return;
        }

        $this->routes[$route] = $result;
    }

    /**
     * @inheritdoc
     */
    public function registerRestful(string $route, callable $action, array $allowedMethods = []): void
    {
        $this->restfulRoutes[] = new RestfulRoute($route, $action, $allowedMethods);
    }

    /**
     * @inheritdoc
     */
    public function getRestfulRoutes(): array
    {
        return $this->restfulRoutes;
    }
}
