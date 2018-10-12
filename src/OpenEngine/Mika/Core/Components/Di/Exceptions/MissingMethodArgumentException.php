<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Di\Exceptions;

use SebastianBergmann\Diff\Exception;

class MissingMethodArgumentException extends \Exception implements DiExceptionInterface
{
}
