<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Interfaces;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface RestfulRouteInterface
 * @package OpenEngine\Mika\Core\Components\Route\Interfaces
 */
interface RestfulRouteInterface
{
    /**
     * @return string
     */
    public function getRoute(): string;

    /**
     * @return callable Any callable as route action. Callable must return {@see ResponseInterface}
     * @see ResponseInterface
     */
    public function getAction(): callable;

    /**
     * @return array Allowed http methods for this route
     */
    public function getAllowedMethods(): array;
}
