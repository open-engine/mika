<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Http\Message\Uri\Traits;

trait HostTrait
{
    /**
     * @var array
     */
    private $defaultPorts = [
        'ftp' => 21,
        'http' => 80,
        'https' => 443,
        'imap' => 143,
        'ldap' => 389,
        'nntp' => 119,
        'pop' => 110,
        'telnet' => 23,
    ];

    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int|null
     */
    private $port;

    /**
     * Retrieve the scheme component of the URI.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority(): string
    {
        $result = '';

        if (!empty($this->getUserInfo())) {
            $result .= $this->getUserInfo() . '@';
        }

        $result .= $this->getHost();
        $result .= $this->getPort() === null ? '' : ':' . $this->getPort();

        return $result;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo(): string
    {
        $result = $this->user;

        if (!empty($this->getPassword())) {
            $result .= ':' . $this->getPassword();
        }

        return $result;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * If no host is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     */
    public function getPort(): ?int
    {
        if ($this->isDefaultPort()) {
            return null;
        }

        return $this->port;
    }

    /**
     * @return string
     */
    private function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $scheme
     * @return $this
     */
    private function setScheme(?string $scheme): self
    {
        if ($scheme !== null) {
            $this->scheme = strtolower($scheme);
        }

        return $this;
    }

    /**
     * @param string $user
     * @return $this
     */
    private function setUser(?string $user): self
    {
        $this->user = $user ?? '';
        return $this;
    }

    /**
     * @param null|string $password
     * @return $this
     */
    private function setPassword(?string $password): self
    {
        $this->password = $password ?? '';
        return $this;
    }

    /**
     * @param string $host
     * @return $this
     */
    private function setHost(?string $host): self
    {
        $this->host = $host ?? '';
        return $this;
    }

    /**
     * @param int|null $port
     * @return $this
     */
    private function setPort(?int $port): self
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return bool
     */
    private function isDefaultPort(): bool
    {
        if ($this->port === null) {
            return true;
        }

        return
            isset($this->defaultPorts[$this->getScheme()]) &&
            $this->port === $this->defaultPorts[$this->getScheme()];
    }
}
