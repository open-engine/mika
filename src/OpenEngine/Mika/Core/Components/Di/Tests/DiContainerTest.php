<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Di\Tests;

use OpenEngine\Mika\Core\App;
use OpenEngine\Mika\Core\Components\Di\Exceptions\ClassNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\ServiceNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Tests\Dummy\Bar;
use OpenEngine\Mika\Core\Components\Di\Tests\Dummy\BarInterface;
use OpenEngine\Mika\Core\Components\Di\Tests\Dummy\Baz;
use OpenEngine\Mika\Core\Components\Di\Tests\Dummy\Foo;
use OpenEngine\Mika\Core\Components\Di\Tests\Dummy\FooInterface;
use PHPUnit\Framework\TestCase;

class DiContainerTest extends TestCase
{
    public function testHasMethod(): void
    {
        $this->assertFalse(App::getContainer()->has('unknownClass'));

        App::getContainer()->register('foo', Foo::class);

        $this->assertTrue(App::getContainer()->has('foo'));
    }

    public function testNotFoundException(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        App::getContainer()->get('foo');
    }

    public function testClassNotFoundException(): void
    {
        $this->expectException(ClassNotFoundException::class);

        App::getContainer()->register(FooInterface::class, 'UnknownClass');
        App::getContainer()->get(FooInterface::class);
    }

    public function testRegister(): void
    {
        App::getContainer()->register(FooInterface::class, Foo::class);
        App::getContainer()->register(BarInterface::class, Bar::class);
        App::getContainer()->register(Baz::class);
        
        $this->assertInstanceOf(Foo::class, App::getContainer()->get(FooInterface::class));
    }

    public function testCreatingMethodDepends(): void
    {
        App::getContainer()->register(BarInterface::class, Bar::class);
        $depends = App::getContainer()->createMethodDepends(Foo::class, 'bar', ['login' => 'as']);

        $this->assertArrayHasKey('bar', $depends);
        $this->assertArrayHasKey('login', $depends);
    }
}
