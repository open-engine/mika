<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Helpers;

/**
 * Class Path
 * @package OpenEngine\Mika\Core\Helpers
 */
class Path
{
    /**
     * @var string[]
     */
    public static $pathNames;
    /**
     * @var string
     */
    private static $root;

    /**
     * Get root directory
     *
     * @todo throw exception when $root is not defined
     * @return string
     */
    public static function getRoot(): string
    {
        return static::$root;
    }

    /**
     * Set path to root directory
     *
     * @param string $root
     */
    public static function setRoot(string $root): void
    {
        static::$root = $root;
    }

    /**
     * Path to web directory
     *
     * @return string
     */
    public static function getWeb(): string
    {
        return static::get('web');
    }

    /**
     * Path to src directory
     *
     * @return string
     */
    public static function getSrc(): string
    {
        return static::get('src');
    }

    /**
     * Alias of
     * ```
     * Path::getSrc() . '/' . $path
     * ```
     *
     * @param string $path
     * @return string
     */
    public static function src(string $path): string
    {
        return static::getSrc() . '/' . $path;
    }

    /**
     * Get absolute path
     *
     * Alias of
     * ```
     * Path::get() . '/' . $path
     * ```
     * @param string $path
     * @return string
     */
    public static function get(string $path): string
    {
        return static::getRoot() . '/' . $path;
    }

    /**
     * Get path by name
     *
     * @param string $name
     * @return string|null
     */
    public static function getName(string $name): ?string
    {
        if (isset(static::$pathNames[$name])) {
            return static::get(static::$pathNames[$name]);
        }

        return null;
    }

    /**
     * Add path
     *
     * @param string $name
     * @param string $path
     */
    public static function addName(string $name, string $path): void
    {
        static::$pathNames[$name] = $path;
    }

    /**
     * @param string $namespace
     * @return string
     */
    public static function getPathByNamespace(string $namespace): string
    {
        return static::src(\str_replace('\\', '/', $namespace));
    }
}
