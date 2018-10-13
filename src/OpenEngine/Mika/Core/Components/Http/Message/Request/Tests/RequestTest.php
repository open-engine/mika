<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Http\Message\Request\Tests;

use OpenEngine\Mika\Core\Components\Http\Message\Request\Request;
use OpenEngine\Mika\Core\Components\Http\Message\Request\RequestFactory;
use OpenEngine\Mika\Core\Components\Http\Message\Uri\UriFactory;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testFactory(): void
    {
        $request = (new RequestFactory())->createRequest('get', (new UriFactory)->createUri('http://localhost:8021'));
        self::assertStringEndsWith('GET', $request->getMethod());
        self::assertArrayHasKey('host2', $request->getHeaders());
        self::assertStringEndsWith('localhost', $request->getHeaderLine('host'));
    }

    public function testTarget(): void
    {
        $request = new Request((new UriFactory)->createUri('http://localhost:8021/test/index.php?foo=bar'));
        self::assertStringEndsWith('/test/index.php?foo=bar', $request->getRequestTarget());
        self::assertStringEndsWith('/foo', $request->withRequestTarget('/foo')->getRequestTarget());
    }

    public function testBase(): void
    {
        $request = new Request((new UriFactory)->createUri('http://localhost:8021/test/index.php?foo=bar'));
        self::assertStringEndsWith('/test/index.php?foo=bar', $request->getRequestTarget());
    }
}
