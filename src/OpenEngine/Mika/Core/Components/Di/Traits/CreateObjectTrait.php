<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Di\Traits;

use OpenEngine\Mika\Core\Components\Di\Exceptions\ClassNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\MethodNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\MissingMethodArgumentException;
use OpenEngine\Mika\Core\Helpers\VarHelper;
use ReflectionClass;
use ReflectionNamedType;

trait CreateObjectTrait
{
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
            if ($type !== null && !VarHelper::isScalar($type->getName())) {
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

            switch ($type->getName()) {
                case 'int':
                    $result[$name] = (int)$knownParams[$name];
                    break;

                case 'float':
                    $result[$name] = (float)$knownParams[$name];
                    break;

                case 'bool':
                    $result[$name] = (bool)$knownParams[$name];
                    break;

                case 'string':
                default:
                    $result[$name] = (string)$knownParams[$name];
                    break;
            }
        }


        return $result;
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
}
