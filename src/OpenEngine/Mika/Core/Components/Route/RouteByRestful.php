<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route;

use Psr\Http\Message\ResponseInterface;

class RouteByRestful
{
    public function hasRoute(string $route): bool
    {
        return false;
    }

    public function callControllerAction(): ResponseInterface
    {

    }
}
