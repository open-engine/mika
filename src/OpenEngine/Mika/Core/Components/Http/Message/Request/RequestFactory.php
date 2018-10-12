<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Http\Message\Request;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class RequestFactory implements RequestFactoryInterface
{
    /**
     * Create a new request.
     *
     * @param string $method The HTTP method associated with the request.
     * @param UriInterface $uri The URI associated with the request. If
     *     the value is a string, the factory MUST create a UriInterface
     *     instance based on it.
     *
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (!$uri instanceof UriInterface) {
            throw new \InvalidArgumentException('Argument $uri must be instance of Psr\Http\Message\UriInterface');
        }

        return new Request($uri, null, [], $method);
    }
}
