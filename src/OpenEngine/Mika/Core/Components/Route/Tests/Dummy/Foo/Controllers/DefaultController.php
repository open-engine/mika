<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Tests\Dummy\Foo\Controllers;

use OpenEngine\Http\Message\Response\Response;

class DefaultController
{
    public function defaultAction(): Response
    {
        return new Response('I am default method of the DefaultController');
    }
}
