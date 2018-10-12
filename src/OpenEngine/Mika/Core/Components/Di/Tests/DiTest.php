<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Di\Tests;

use OpenEngine\Mika\Core\App;
use OpenEngine\Mika\Core\Components\Di\Di;
use OpenEngine\Mika\Core\Components\Di\DiConfig;
use OpenEngine\Mika\Core\Components\Di\Exceptions\ClassNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\MissingMethodArgumentException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\ServiceNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Tests\Dummy\Bar;
use OpenEngine\Mika\Core\Components\Di\Tests\Dummy\BarInterface;
use OpenEngine\Mika\Core\Components\Di\Tests\Dummy\Baz;
use OpenEngine\Mika\Core\Components\Di\Tests\Dummy\Foo;
use OpenEngine\Mika\Core\Components\Di\Tests\Dummy\FooInterface;
use OpenEngine\Mika\Core\Helpers\Path;
use PHPUnit\Framework\TestCase;

class DiTest extends TestCase
{

    public function testHasMethod(): void
    {
        $this->assertFalse($this->getDi()->has('unknownClass'));
        $this->assertTrue($this->getDi()->has('foo'));
    }

    public function testNotFoundException(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->getDi()->get('unknownService');
    }

    public function testClassNotFoundException(): void
    {
        $this->expectException(ClassNotFoundException::class);
        $this->getDi()->get('service');
    }

    public function testRegister(): void
    {
        $this->assertInstanceOf(Foo::class, $this->getDi()->get(FooInterface::class));
    }

    public function testCreatingMethodDepends(): void
    {
        $depends = $this->getDi()->createMethodDepends(Foo::class, 'bar', ['login' => 'as']);

        $this->assertArrayHasKey('bar', $depends);
        $this->assertArrayHasKey('login', $depends);
    }

    public function testMissingArgumentException(): void
    {
        $this->expectException(MissingMethodArgumentException::class);
        $this->getDi()->createMethodDepends(Foo::class, 'bar');
    }

    public function testCreateObject(): void
    {
        $obj = $this->getDi()->createObject(Foo::class);
        $this->assertInstanceOf(Foo::class, $obj);
    }

    protected function setUp()
    {
        Path::setRoot(getenv('MIKA_ROOT_DIR'));
    }

    private function getDi(): Di
    {
        $diConfig = new DiConfig();
        $diConfig->register('foo', Foo::class);
        $diConfig->register('service', 'UnknownClass');

        $diConfig->register(FooInterface::class, Foo::class);
        $diConfig->register(BarInterface::class, Bar::class);
        $diConfig->register(Baz::class);


        return new Di($diConfig);
    }
}
