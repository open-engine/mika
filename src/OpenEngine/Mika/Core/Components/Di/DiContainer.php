<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Di;

use OpenEngine\Mika\Core\Components\Di\Exceptions\ClassNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\ServiceNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionNamedType;

class DiContainer implements \Psr\Container\ContainerInterface
{
    /**
     * @var string[]
     */
    private $services = [];

    /**
     * @var object[]
     */
    private $singletons = [];

    /**
     * Register service
     *
     * @param string $id Often it SomeInterface::class
     * @param string $service ConcreteClass::class
     */
    public function register(string $id, ?string $service = null): void
    {
        if ($service === null) {
            $service = $id;
        }

        $this->services[$id] = $service;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return object Entry.
     */
    public function get($id): object
    {
        if (!$this->has($id)) {
            throw new ServiceNotFoundException('Service ' . $id . ' is not found');
        }

        $singleton = $this->getSingleton($id);

        if ($singleton !== null) {
            return $singleton;
        }

        return $this->createObject($this->getService($id));
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id): bool
    {
        return $this->getService($id) !== null;
    }

    /**
     * Create method depends
     *
     * Method returns list of params with initialized depends.
     * All services for method will be initialized automatically.
     * Other params you must specify on parameter $knownParams.
     *
     * For example:
     * ```
     * class Foo {
     *      public method bar(BazInterface $baz, Baz2Interface $baz2, string $baz3, int $param4): void
     *      {
     *          // ... code ...
     *      }
     * }
     *
     * // $baz and $baz2 will initialized by automatically.
     * // $baz3 and $param4 you must specify
     * App::getContainer()->createMethodDepends(Foo::class, "bar", ['baz3' => 'Test', 'param4' => 13])
     *
     * ```
     *
     * @param string $className
     * @param string $methodName
     * @param array $knownParams You can specify some parameter values if you already know what method needs
     * @return array
     */
    public function createMethodDepends(string $className, string $methodName, array $knownParams = []): array
    {
        $result = [];

        /**
         * @var ReflectionNamedType $type
         */
        foreach ($this->getMethodParams($className, $methodName) as $name => $type) {
            $result[$name] = $knownParams[$name] ?? $this->get($type->getName());
        }

        return $result;
    }

    /**
     * @param string $id
     * @return string|null
     */
    private function getService(string $id): ?string
    {
        if (isset($this->services[$id])) {
            return $this->services[$id];
        }

        return null;
    }

    /**
     * @param string $id
     * @return \object|null
     */
    private function getSingleton(string $id): ?object
    {
        if (isset($this->singletons[$id])) {
            return $this->singletons[$id];
        }

        return null;
    }

    /**
     * @param string $className
     * @return object
     * @throws ClassNotFoundException
     */
    private function createObject(string $className): object
    {
        $params = $this->createMethodDepends($className, '__construct');

        try {
            $reflector = new ReflectionClass($className);
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException('Class ' . $className . ' is not found');
        }

        return $reflector->newInstanceArgs($params);
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return array
     */
    private function getMethodParams(string $className, string $methodName): array
    {
        $result = [];

        try {
            $method = new \ReflectionMethod($className, $methodName);
        } catch (\ReflectionException $e) {
            return $result;
        }

        foreach ($method->getParameters() as $parameter) {
            $result [$parameter->name] = $parameter->getType();
        }

        return $result;
    }
}
