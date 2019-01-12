<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Tests\Dummy\Foo\Controllers;

use OpenEngine\Http\Message\Response\Response;
use Psr\Http\Message\ResponseInterface;

class BarController
{
    /**
     * @return ResponseInterface
     */
    public function bazAction(): ResponseInterface
    {
        return new Response('I am an answer from BarController::bazAction');
    }
}
