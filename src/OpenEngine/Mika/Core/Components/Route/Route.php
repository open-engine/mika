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
use OpenEngine\Mika\Core\Components\Route\Traits\RouteByNamespaceTrait;
use OpenEngine\Mika\Core\Components\Route\Traits\RouteParserTrait;
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
    use RouteByNamespaceTrait;

    /**
     * Default route name
     */
    public const DEFAULT_ROUTE = 'default';

    /**
     * @var RequestInterface
     */
    private $request;

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
        $this->parseUri($request);
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
    }
}
