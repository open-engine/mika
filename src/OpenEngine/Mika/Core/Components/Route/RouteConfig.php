<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route;

use OpenEngine\Mika\Core\Components\Route\Interfaces\RouteConfigInterface;
use OpenEngine\Mika\Core\Helpers\Path;

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
        $controllerFiles = glob(Path::getPathByNamespace($controllersNamespace . '/*'));

        $result = [];

        foreach ($controllerFiles as $controllerFile) {
            $result[] = $controllersNamespace . '\\' . basename($controllerFile, '.php');
        }

        if (empty($result)) {
            return;
        }

        $this->routes[$route] = $result;
    }
}
