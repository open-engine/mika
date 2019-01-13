<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Tests\Dummy;

use OpenEngine\Http\Message\Stream\StreamFactory;
use OpenEngine\Mika\Core\Components\Route\Events\AfterCallActionEvent;

class RouteEventListener
{
    public static function changeBody(AfterCallActionEvent $event): AfterCallActionEvent
    {
        if ($event->getActionName() === 'checkEventAction') {
            $response = $event->getResponse()->withBody((new StreamFactory())->createStream('body has changed by listener'));
            $event->setResponse($response);
        }

        return $event;
    }
}
