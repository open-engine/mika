<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Http\Exceptions;

class NotFoundHttpException extends \Exception
{
    /**
     * NotFoundException constructor.
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, \Throwable $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}
