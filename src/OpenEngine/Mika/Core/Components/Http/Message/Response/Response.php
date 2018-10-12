<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Http\Message\Response;

use OpenEngine\Mika\Core\Components\Http\Message\Stream\StreamFactory;
use OpenEngine\Mika\Core\Components\Http\Traits\BodyTrait;
use OpenEngine\Mika\Core\Components\Http\Traits\HeadersTrait;
use OpenEngine\Mika\Core\Components\Http\Traits\ProtocolVersionTrait;
use Psr\Http\Message\ResponseInterface;

class Response implements ResponseInterface
{
    use HeadersTrait;
    use BodyTrait;
    use ProtocolVersionTrait;

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $reasonPhrase;

    /**
     * Response constructor.
     * @param string $body
     * @param int $code
     * @param string $reasonPhrase
     * @param array $headers
     * @param string $version
     */
    public function __construct(
        string $body,
        int $code = 200,
        string $reasonPhrase = '',
        array $headers = [],
        string $version = '1.1'
    ) {
        $this->body = (new StreamFactory())->createStream($body);
        $this->code = $code;
        $this->headers = $headers;
        $this->protocolVersion = $version;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode(): int
    {
        return $this->code;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = ''): self
    {
        $new = clone $this;
        $new->code = $code;
        $new->reasonPhrase = $reasonPhrase;
        return $new;

    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}
