<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Di\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class ClassNotFoundException extends \Exception implements ContainerExceptionInterface
{
}
