<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core;

use OpenEngine\Di\Di;
use OpenEngine\Di\DiConfig;
use OpenEngine\Di\Exceptions\ClassNotFoundException;
use OpenEngine\Di\Exceptions\MethodNotFoundException;
use OpenEngine\Di\Exceptions\MissingMethodArgumentException;
use OpenEngine\Http\Exceptions\NotFoundHttpException;
use OpenEngine\Http\Message\Request\RequestFactory;
use OpenEngine\Http\Message\Uri\UriFactory;
use OpenEngine\Mika\Core\Components\Route\Interfaces\RouteInterface;
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

        foreach ($response->getHeaders() as $name => $header) {
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
