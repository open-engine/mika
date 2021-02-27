<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Route\Traits;

use OpenEngine\Mika\Core\Components\Route\Route;
use Psr\Http\Message\RequestInterface;

trait RouteParserTrait
{
    /**
     * @var string
     */
    private $main;

    /**
     * @var string
     */
    private $secondary;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $current;

    /**
     * @inheritdoc
     */
    public function getCurrent(): string
    {
        return $this->current;
    }

    /**
     * @inheritdoc
     */
    public function getMain(): string
    {
        return $this->main;
    }

    /**
     * @inheritdoc
     */
    public function getSecondary(): string
    {
        return $this->secondary;
    }

    /**
     * @inheritdoc
     */
    public function getAction(): string
    {
        return $this->action;
    }

    private function parseUri(RequestInterface $request): void
    {
        $parts = explode('/', $request->getUri()->getPath());

        $this->main = $this->getPart($parts, 1);
        $this->secondary = $this->getPart($parts, 2);
        $this->action = $this->getPart($parts, 3);

        $this->current =
            '/' . $this->main .
            '/' . $this->secondary .
            '/' . $this->action;
    }

    /**
     * @param array $parts
     * @param $key
     * @return string
     */
    private function getPart(array $parts, $key): string
    {
        return !empty($parts[$key]) && $parts[$key] !== '/' ? $parts[$key] : Route::DEFAULT_ROUTE;
    }

}
