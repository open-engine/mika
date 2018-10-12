<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Interfaces;

interface RouteConfigInterface
{
    /**
     * Returns array where keys is route name
     * And values is some controller classes names
     *
     * ```
     * array (
     *      'foo' => [
     *          'Acme\Foo\Controllers\FooController',
     *          'Acme\Foo\Controllers\BarController',
     *       ],
     *      'bar' => [
     *          'Acme\Bar\Controllers\BarController',
     *          'Acme\Bar\Controllers\BazController',
     *       ],
     * )
     *
     * ```
     *
     * @return array
     */
    public function getRoutes(): array;

    /**
     * Add route for controllers
     *
     * If you wand that your controllers be able to handle rout starts as '/foo',
     * You can do like this:
     * ```
     * add('foo', 'Your\Controllers\Namespaces');
     * ```
     *
     * Router will call method 'bazAction' in class 'Your\Controllers\Namespaces\BarController'
     * when current rout is
     * ```
     * /foo/bar/baz
     * ```
     *
     * Or calls 'testAction' in class 'Your\Controllers\Namespaces\AcmeController'
     * when current route is
     * ```
     * /foo/acme/test
     * ```
     *
     * Router wil calls 'indexAction' in class 'Your\Controllers\Namespaces\DefaultController'
     * when current route is
     * ```
     * /foo
     * ```
     *
     * @param string $route
     * @param string $controllersNamespace
     */
    public function register(string $route, string $controllersNamespace): void;
}
