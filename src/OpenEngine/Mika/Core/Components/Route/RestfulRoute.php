<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route;

use OpenEngine\Mika\Core\Components\Route\Interfaces\RestfulRouteInterface;

/**
 * Class RestfulRoute
 * @package OpenEngine\Mika\Core\Components\Route
 */
class RestfulRoute implements RestfulRouteInterface
{
    /**
     * @var string
     */
    private $route;

    /**
     * @var callable
     */
    private $action;

    /**
     * @var array
     */
    private $allowedMethods;

    /**
     * RestfulRoute constructor.
     * @param string $route
     * @param callable $action
     * @param array $allowedMethods
     */
    public function __construct(string $route, callable $action, array $allowedMethods = [])
    {
        $this->route = $route;
        $this->action = $action;
        $this->allowedMethods = $allowedMethods;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @inheritdoc
     */
    public function getAction(): callable
    {
        return $this->action;
    }

    /**
     * @inheritdoc
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}
