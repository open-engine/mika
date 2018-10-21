<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Orm\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Repository\RepositoryFactory;
use OpenEngine\Mika\Core\Components\Orm\Tests\Entity\Foo;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DoctrineTest extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    protected function setUp()
    {
        $paths = array(__DIR__ . '/Entity');
        $isDevMode = false;

        $dbParams = array(
            'driver' => 'pdo_mysql',
            'user' => 'root',
            'password' => '',
            'dbname' => 'foo',
        );

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $this->entityManager = EntityManager::create($dbParams, $config);

        parent::setUp();
    }

    public function testEntityManager(): void
    {
        self::assertInstanceOf(EntityManagerInterface::class, $this->entityManager);
    }

    public function testRepository(): void
    {
        $foo = $this->entityManager->getRepository(Foo::class);
        self::assertInstanceOf(EntityRepository::class, $foo);
    }
}
