<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Http\Message\Uri\Tests;

use OpenEngine\Mika\Core\Components\Http\Message\Uri\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    const URI = 'http://username:password@hostname:9090/path?arg=value#anchor';

    public function testBase(): void
    {
        $uri = new Uri(self::URI);
        $this->assertStringEndsWith(self::URI, $uri->__toString());
    }

    public function testClone(): void
    {
        $uri = new Uri(self::URI);
        $new = $uri
            ->withScheme('hTtPS')
            ->withUserInfo('john', 'doe')
            ->withHost('localhost')
            ->withPort(443)
            ->withPath('main/index.php')
            ->withQuery('foo=bar&baz=baz2')
            ->withFragment('fragment');

        $this->assertStringEndsWith('https://john:doe@localhost/main/index.php?foo=bar&baz=baz2#fragment',
            $new->__toString());
    }

    public function testMethods(): void
    {
        $uri = new Uri(self::URI);
        $this->assertStringEndsWith('http', $uri->getScheme());
        $this->assertStringEndsWith('username:password@hostname:9090', $uri->getAuthority());
        $this->assertStringEndsWith('username:password', $uri->getUserInfo());
        $this->assertStringEndsWith('hostname', $uri->getHost());
        $this->equalTo(9090, $uri->getPort());
        $this->assertStringEndsWith('path', $uri->getPath());
        $this->assertStringEndsWith('arg=value', $uri->getQuery());
        $this->assertStringEndsWith('anchor', $uri->getFragment());
    }
}
