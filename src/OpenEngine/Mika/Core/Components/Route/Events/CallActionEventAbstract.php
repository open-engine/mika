<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Events;

abstract class CallActionEventAbstract
{
    /**
     * @var string
     */
    private $controllerName;

    /**
     * @var string
     */
    private $actionName;

    /**
     * @var array
     */
    private $actionParams;

    /**
     * CallActionEventAbstract constructor.
     * @param string $controllerName
     * @param string $actionName
     * @param array $actionParams
     */
    public function __construct(string $controllerName, string $actionName, array $actionParams)
    {
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
        $this->actionParams = $actionParams;
    }

    /**
     * @return string
     */
    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * @return array
     */
    public function getActionParams(): array
    {
        return $this->actionParams;
    }
}
