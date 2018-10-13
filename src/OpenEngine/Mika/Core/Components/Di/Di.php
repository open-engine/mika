<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Di;

use OpenEngine\Mika\Core\Components\Di\Exceptions\ClassNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\MethodNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\MissingMethodArgumentException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\ServiceNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Interfaces\DiConfigInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;

class Di implements ContainerInterface
{
    /**
     * Created services
     *
     * @var object[]
     */
    private $singletons;

    /**
     * Registered service names
     *
     * @var string[]
     */
    private $services;

    /**
     * Di constructor.
     * @param DiConfigInterface $diConfig
     */
    public function __construct(DiConfigInterface $diConfig)
    {
        $diConfig->registerObject(ContainerInterface::class, $this);
        $this->services = $diConfig->getServices();
        $this->singletons = $diConfig->getServiceObjects();
    }

    /**
     * {@inheritdoc}
     * @throws MethodNotFoundException
     * @throws MissingMethodArgumentException
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
     * @inheritdoc
     */
    public function has($id): bool
    {
        return $this->getService($id) !== null;
    }

    /**
     * @param string $className
     * @return object
     * @throws ClassNotFoundException
     * @throws MethodNotFoundException
     * @throws MissingMethodArgumentException
     */
    public function createObject(string $className): object
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
     * @throws MethodNotFoundException
     * @throws MissingMethodArgumentException
     */
    public function createMethodDepends(string $className, string $methodName, array $knownParams = []): array
    {
        $result = [];

        /**
         * @var ReflectionNamedType $type
         */
        foreach ($this->getMethodParams($className, $methodName) as $name => $type) {
            if ($type !== null && !$this->isScalar($type->getName())) {
                $result[$name] = $this->get($type->getName());
                continue;
            }

            if (!isset($knownParams[$name])) {
                throw new MissingMethodArgumentException('Missing argument ' . $name . ' for method ' . $methodName);
            }

            if ($type === null) {
                $result[$name] = $knownParams[$name];
                continue;
            }

            $this->addCastedVar($result, $name, $type->getName(), $knownParams[$name]);
        }

        return $result;
    }

    /**
     * @param array $methodDepends
     * @param string $paramName
     * @param string $type
     * @param mixed $var
     */
    private function addCastedVar(array &$methodDepends, string $paramName, string $type, $var): void
    {
        switch ($type) {
            case 'int':
                $methodDepends[$paramName] = (int)$var;
                break;

            case 'float':
                $methodDepends[$paramName] = (float)$var;
                break;

            case 'bool':
                $methodDepends[$paramName] = (bool)$var;
                break;

            case 'string':
            default:
                $methodDepends[$paramName] = (string)$var;
                break;
        }
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
     * @param string $methodName
     * @return array
     * @throws MethodNotFoundException
     */
    private function getMethodParams(string $className, string $methodName): array
    {
        $result = [];

        try {
            $method = new \ReflectionMethod($className, $methodName);
        } catch (\ReflectionException $e) {
            if ($methodName === '__construct') {
                return $result;
            }

            throw new MethodNotFoundException($e->getMessage());
        }

        foreach ($method->getParameters() as $parameter) {
            $result [$parameter->name] = $parameter->getType();
        }

        return $result;
    }

    /**
     * @param string $typeName
     * @return bool
     */
    private function isScalar(string $typeName): bool
    {
        return \in_array($typeName, ['int', 'string', 'float', 'bool']);
    }
}
