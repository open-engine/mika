<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Interfaces;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface RouteInterface
 * @package OpenEngine\Mika\Core\Components\Route\Interfaces
 */
interface RouteInterface
{
    /**
     * Get current route
     *
     * <pre>
     * Method returns '/foo/bar/baz' if url is '/foo/bar/baz'.
     * Method returns '/foo/bar/default' if url is '/foo/bar'.
     * Method returns '/foo/default/default' if url is '/foo'.
     * Method returns '/default/default/default' if url is '/'.
     * </pre>
     *
     * @return string
     */
    public function getCurrent(): string;

    /**
     * Get main route.
     *
     * <pre>
     * Method returns 'foo' if url is '/foo/bar/baz'.
     * Method returns 'default' if url is '/'.
     * </pre>
     *
     * @return string
     */
    public function getMain(): string;

    /**
     * Get controller part of route.
     *
     * <pre>
     * Method returns 'bar' if url is '/foo/bar/baz'.
     * Method returns 'default' if url is '/foo'.
     * </pre>
     *
     * @return string
     */
    public function getSecondary(): string;

    /**
     * Get controller part of route.
     *
     * <pre>
     * Method returns 'baz' if url is '/foo/bar/baz'.
     * Method returns 'default' if url is '/foo' or '/foo/bar'
     * </pre>
     *
     * @return string
     */
    public function getAction(): string;

    /**
     * This method finds needle controller from route,
     * creates instance and coll needle method from route
     */
    public function callControllerAction(): ResponseInterface;

    /**
     * Get all registered routes
     *
     * @return array
     */
    public function getRoutes(): array;
}
