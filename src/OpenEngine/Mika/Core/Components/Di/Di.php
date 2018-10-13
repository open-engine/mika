<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Di;

use OpenEngine\Mika\Core\Components\Di\Exceptions\MethodNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\MissingMethodArgumentException;
use OpenEngine\Mika\Core\Components\Di\Exceptions\ServiceNotFoundException;
use OpenEngine\Mika\Core\Components\Di\Interfaces\DiConfigInterface;
use OpenEngine\Mika\Core\Components\Di\Traits\CreateObjectTrait;
use Psr\Container\ContainerInterface;

class Di implements ContainerInterface
{
    use CreateObjectTrait;

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
}
