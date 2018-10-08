<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Helpers;

/**
 * Class Path
 * @package OpenEngine\Mika\Core\Helpers
 */
class Path
{
    /**
     * @var string
     */
    private static $root;

    /**
     * @var string[]
     */
    public static $pathNames;

    /**
     * Get root directory
     *
     * @return string
     */
    public static function getRoot(): string
    {
        return self::$root;
    }

    /**
     * Set path to root directory
     *
     * @param string $root
     */
    public static function setRoot(string $root): void
    {
        self::$root = $root;
    }

    /**
     * Path to web directory
     *
     * @return string
     */
    public static function getWeb(): string
    {
        return self::get('web');
    }

    /**
     * Get absolute path
     *
     * @param string $path
     * @return string
     */
    public static function get(string $path): string
    {
        return self::getRoot() . '/' . $path;
    }

    /**
     * Get path by name
     *
     * @param string $name
     * @return string|null
     */
    public static function getName(string $name): ?string
    {
        if (isset(self::$pathNames[$name])) {
            return self::get(self::$pathNames[$name]);
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
        self::$pathNames[$name] = $path;
    }
}
