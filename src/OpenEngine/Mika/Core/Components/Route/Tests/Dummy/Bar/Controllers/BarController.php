<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Tests\Dummy\Bar\Controllers;

use OpenEngine\Mika\Core\Components\Http\Message\Response\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class BarController
{
    /**
     * BarController constructor.
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(StreamFactoryInterface $streamFactory)
    {
        // do nothing
    }

    /**
     * @param RequestInterface $request
     * @return Response
     */
    public function defaultAction(RequestInterface $request): Response
    {
        return new Response('called method is: ' . $request->getMethod());
    }

    /**
     * @param RequestInterface $request
     * @param string $name
     * @param $type
     * @param int $age
     * @return Response
     */
    public function frameworkNameAction(RequestInterface $request, string $name, $type, int $age): Response
    {
        return new Response(
            'method: ' . $request->getMethod() . '; '.
            'name: ' . $name . '; '.
            'type: ' . $type . '; '.
            'age: ' . $age
        );
    }

}
