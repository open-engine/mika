<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core;

use OpenEngine\Mika\Core\Components\Di\Di;
use OpenEngine\Mika\Core\Components\Di\DiConfig;
use OpenEngine\Mika\Core\Components\Di\Exceptions\ClassNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\MethodNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\MissingMethodArgumentException;
use OpenEngine\Mika\Core\Components\Http\Exceptions\NotFoundHttpException;
use OpenEngine\Mika\Core\Components\Http\Message\Request\RequestFactory;
use OpenEngine\Mika\Core\Components\Http\Message\Uri\UriFactory;
use OpenEngine\Mika\Core\Components\Route\Interfaces\RouteInterface;
use OpenEngine\Mika\Core\Components\Route\Route;
use Psr\Http\Message\RequestInterface;

class Mika
{
    /**
     * @param DiConfig $diConfig
     * @throws ClassNotFoundException
     * @throws MethodNotFoundException
     * @throws MissingMethodArgumentException
     * @throws NotFoundHttpException
     */
    public static function run(DiConfig $diConfig): void
    {
        /**
         * @var RouteInterface|Route $route
         */
        $di = self::createDi($diConfig);
        $route = $di->get(RouteInterface::class);

        $response = $route->callControllerAction();

        foreach($response->getHeaders() as $name => $header) {
            header($name . ': ' . $response->getHeaderLine($name));
        }

        print $response->getBody();
    }

    /**
     * @param DiConfig $diConfig
     * @return Di
     */
    private static function createDi(DiConfig $diConfig): Di
    {
        $diConfig->registerObject(RequestInterface::class, self::createRequest());
        return new Di($diConfig);
    }

    /**
     * @return RequestInterface
     */
    private static function createRequest(): RequestInterface
    {
        $uri = (new UriFactory())->createUri();
        return (new RequestFactory())->createRequest($_SERVER['REQUEST_METHOD'] ?? 'GET', $uri);
    }
}
