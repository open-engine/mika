<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Orm\Tests\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

/** @Entity */
class Foo
{
    /**
     * @var int
     * @Id @GeneratedValue @Column(type="string")
     */
    private $id;

    /**
     * @var string
     * @Column(type="string")
     */
    private $name;

    /**
     * @var int
     * @Column(type="string")
     */
    private $age;
}
