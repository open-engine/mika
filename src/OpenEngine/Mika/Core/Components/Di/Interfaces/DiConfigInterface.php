<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Di\Interfaces;

/**
 * Interface DiConfigInterface
 * @package OpenEngine\Mika\Core\Components\Di\Interfaces
 */
interface DiConfigInterface
{
    /**
     * Register service
     *
     * @param string $id Often it SomeInterface::class
     * @param string $service ConcreteClass::class
     */
    public function register(string $id, ?string $service = null): void;

    /**
     * Register service and set object
     *
     * @param string $id Often it SomeInterface::class
     * @param object $service ConcreteObject
     */
    public function registerObject(string $id, object $service): void;

    /**
     * Get all registered services
     *
     * @return string[]
     */
    public function getServices(): array;

    /**
     * Get all registered service objects
     *
     * @return array
     */
    public function getServiceObjects(): array;
}
