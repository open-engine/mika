<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Di\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Class NotFoundException
 * @package OpenEngine\Mika\Core\Components\Di\Exceptions
 */
class ServiceNotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
