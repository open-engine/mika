<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Interfaces;

interface RouteConfigInterface
{
    /**
     * Add route for controllers
     *
     * If you wand that your controllers be able to handle route starts as '/foo',
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
     * You can register restful route
     *
     * ```
     * $config->registerRestful('/users/{id}/profile', '\Foo\BarController::BazAction', ['post', 'get']);
     *
     * $config->registerRestful('/users/', function() {
     *      return new Response('Hello World!');
     * });
     *
     * ```
     *
     * @param string $route
     * @param callable $action
     * @param array $allowedMethods If array is empty it means any http method is allowed
     */
    public function registerRestful(string $route, callable $action, array $allowedMethods = []): void;

    /**
     * Method must return restful routes list
     *
     * @return RestfulRouteInterface[]
     */
    public function getRestfulRoutes(): array;
}
