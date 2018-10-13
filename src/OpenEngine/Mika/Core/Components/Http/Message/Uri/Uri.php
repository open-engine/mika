<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Http\Message\Uri;

use OpenEngine\Mika\Core\Components\Http\Message\Uri\Traits\CloneableHostTrait;
use OpenEngine\Mika\Core\Components\Http\Message\Uri\Traits\CloneableQueryTrait;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    use CloneableHostTrait;
    use CloneableQueryTrait;

    /**
     * Uri constructor.
     * @param null|string $uri
     */
    public function __construct(?string $uri = null)
    {
        if ($uri === null) {
            return;
        }

        $this
            ->setScheme(parse_url($uri, PHP_URL_SCHEME))
            ->setUser(parse_url($uri, PHP_URL_USER))
            ->setPassword(parse_url($uri, PHP_URL_PASS))
            ->setHost(parse_url($uri, PHP_URL_HOST))
            ->setPort(parse_url($uri, PHP_URL_PORT))
            ->setPath(parse_url($uri, PHP_URL_PATH))
            ->setQuery(parse_url($uri, PHP_URL_QUERY))
            ->setFragment(parse_url($uri, PHP_URL_FRAGMENT));
    }

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        return
            $this->getBasePart() .
            $this->getTailPart();
    }

    /**
     * Return an empty string or [scheme://][user:password@]host[:port]
     *
     * @return string
     */
    private function getBasePart(): string
    {
        $scheme = empty($this->getScheme()) ? '' : $this->getScheme() . ':';
        $authority = empty($this->getAuthority()) ? '' : '//' . $this->getAuthority();

        return $scheme . $authority;
    }

    /**
     * Return an empty string or /[path][?query][#fragment]
     *
     * @return string
     */
    private function getTailPart(): string
    {
        $path = '/';

        if (!empty($this->getPath())) {
            $path = strpos($this->getPath(), '/') === 0 ? '' : '/';
            $path .= $this->getPath();
        }

        $query = empty($this->getQuery()) ? '' : '?' . $this->getQuery();
        $fragment = empty($this->getFragment()) ? '' : '#' . $this->getFragment();

        return $path . $query . $fragment;
    }
}
