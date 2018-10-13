<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Http\Message\Response\Tests;

use OpenEngine\Mika\Core\Components\Http\Message\Response\Response;
use OpenEngine\Mika\Core\Components\Http\Message\Stream\StreamFactory;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testBody(): void
    {
        $response = new Response('It is response body');
        self::assertStringEndsWith('It is response body', $response->getBody()->__toString());
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testCode(): void
    {
        $response = new Response('It is response body', 201);
        self::assertStringEndsWith('It is response body', $response->getBody()->__toString());
        self::assertEquals(201, $response->getStatusCode());
    }

    public function testReason(): void
    {
        $response = new Response('It is response body', 403, 'Something went wrong');
        self::assertStringEndsWith('It is response body', $response->getBody()->__toString());
        self::assertNotEquals(200, $response->getStatusCode());
        self::assertStringEndsWith('wrong', $response->getReasonPhrase());
    }

    public function testHeaders(): void
    {
        $response = new Response('', 200, '', [
            'Content-type' => ['charser=utf-8;text/html'],
            'Accept' => ['utf-8', 'ISO-8859-1', 'q=0.7']
        ]);

        self::assertArrayHasKey('content-type', $response->getHeaders());
        self::assertStringEndsWith('utf-8, ISO-8859-1, q=0.7', $response->getHeaderLine('Accept'));
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testWithoutHeader(): void
    {
        $response = new Response('', 200, '', [
            'Content-type' => ['charser=utf-8;text/html'],
            'Accept' => ['utf-8', 'ISO-8859-1', 'q=0.7']
        ]);

        $response = $response->withoutHeader('Content-type');

        self::assertArrayNotHasKey('content-type', $response->getHeaders());
        self::assertArrayHasKey('accept', $response->getHeaders());
    }

    public function testWithBody(): void
    {
        $response = new Response('Foo');

        $response = $response->withBody((new StreamFactory)->createStream('Bar'));

        self::assertStringEndsWith('Bar', $response->getBody()->__toString());
    }
}
