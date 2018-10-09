<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Di;

use OpenEngine\Mika\Core\Components\Di\Interfaces\DiConfigInterface;

class DiConfig implements DiConfigInterface
{
    /**
     * @var string[]
     */
    private $services = [];

    /**
     * @var object[]
     */
    private $serviceObjects = [];

    /**
     * @inheritdoc
     */
    public function register(string $id, ?string $service = null): void
    {
        if ($service === null) {
            $service = $id;
        }

        $this->services[$id] = $service;
    }

    /**
     * @inheritdoc
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @inheritdoc
     */
    public function registerObject(string $id, object $service): void
    {
        $this->register($id);
        $this->serviceObjects[$id] = $service;
    }

    /**
     * @inheritdoc
     */
    public function getServiceObjects(): array
    {
        return $this->serviceObjects;
    }
}
