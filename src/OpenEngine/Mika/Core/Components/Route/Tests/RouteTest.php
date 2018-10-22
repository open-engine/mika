<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Tests;

use OpenEngine\Mika\Core\Components\Di\DiConfig;
use OpenEngine\Mika\Core\Components\Di\Exceptions\MethodNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\MissingMethodArgumentException;
use OpenEngine\Mika\Core\Components\Http\Exceptions\NotFoundHttpException;
use OpenEngine\Mika\Core\Components\Http\Message\Stream\StreamFactory;
use OpenEngine\Mika\Core\Components\Route\Interfaces\RouteConfigInterface;
use OpenEngine\Mika\Core\Components\Route\Interfaces\RouteInterface;
use OpenEngine\Mika\Core\Components\Route\Route;
use OpenEngine\Mika\Core\Components\Route\RouteConfig;
use OpenEngine\Mika\Core\Helpers\Path;
use OpenEngine\Mika\Core\Mika;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;

class RouteTest extends TestCase
{
    public function testAddingRoute(): void
    {
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz?key=val&foo2';

        Mika::run($this->getDiConfig());
        $this->expectOutputString('I am an answer from BarController::bazAction');
    }

    public function testDefaultControllerOfRoute(): void
    {
        $_SERVER['REQUEST_URI'] = '/foo';

        Mika::run($this->getDiConfig());
        $this->expectOutputString('I am default method of the DefaultController');
    }

    public function testSolvingMethodDepends(): void
    {
        $_SERVER['REQUEST_URI'] = '/bar/bar';

        Mika::run($this->getDiConfig());
        $this->expectOutputString('called method is: GET');
    }

    public function testSolvingMethodDependsWithParams(): void
    {
        $_SERVER['REQUEST_URI'] = '/bar/bar/frameworkName';
        $_GET['name'] = 'Mika';
        $_GET['type'] = 'Framework';
        $_GET['age'] = '31';

        Mika::run($this->getDiConfig());
        $this->expectOutputString('method: GET; name: Mika; type: Framework; age: 31');
    }

    public function testExceptionMissingArgument(): void
    {
        $_SERVER['REQUEST_URI'] = '/bar/bar/frameworkName';
        $_GET['name'] = 'Mika';
        $_GET['type'] = 'Framework';
        $_GET['age'] = '31';

        unset($_GET['name']);

        $this->expectException(MissingMethodArgumentException::class);
        Mika::run($this->getDiConfig());
    }

    public function testExceptionMethodNotFound(): void
    {
        $_SERVER['REQUEST_URI'] = '/bar/bar/unknown';

        $this->expectException(MethodNotFoundException::class);
        Mika::run($this->getDiConfig());
    }

    public function testExceptionNotFound(): void
    {
        $_SERVER['REQUEST_URI'] = '/bar/unknown';

        $this->expectException(NotFoundHttpException::class);
        Mika::run($this->getDiConfig());
    }

    protected function setUp()
    {
        Path::setRoot(getenv('MIKA_ROOT_DIR'));

        $this->setUpGlobals();
    }

    private function setUpGlobals(): void
    {
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['SERVER_NAME'] = 'kadirov.org';
    }

    private function getDiConfig(): DiConfig
    {
        $routeConfig = new RouteConfig();
        $routeConfig->register(
            'foo',
            'OpenEngine\Mika\Core\Components\Route\Tests\Dummy\Foo\Controllers'
        );

        $routeConfig->register(
            'bar',
            'OpenEngine\Mika\Core\Components\Route\Tests\Dummy\Bar\Controllers'
        );

        $diConfig = new DiConfig();
        $diConfig->register(StreamFactoryInterface::class, StreamFactory::class);
        $diConfig->register(RouteInterface::class, Route::class);
        $diConfig->registerObject(RouteConfigInterface::class, $routeConfig);

        return $diConfig;
    }
}
