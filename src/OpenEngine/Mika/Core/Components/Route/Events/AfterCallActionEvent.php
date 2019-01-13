<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Events;

use Psr\Http\Message\ResponseInterface;

/**
 * Event will be triggered by Route after calling controller action
 *
 * @package OpenEngine\Mika\Core\Components\Route\Events
 */
class AfterCallActionEvent extends CallActionEventAbstract
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * AfterCallActionEvent constructor.
     * @param string $controllerName
     * @param string $actionName
     * @param array $actionParams
     * @param $response
     */
    public function __construct(string $controllerName, string $actionName, array $actionParams, $response)
    {
        $this->response = $response;

        parent::__construct($controllerName, $actionName, $actionParams);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }
}
