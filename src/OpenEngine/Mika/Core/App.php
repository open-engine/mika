<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core;

use OpenEngine\Mika\Core\Components\Di\DiContainer;

class App
{
    /**
     * @var DiContainer
     */
    private static $container;

    /**
     * @return DiContainer
     */
    public static function getContainer(): DiContainer
    {
        if (static::$container === null) {
            static::$container = new DiContainer();
        }

        return static::$container;
    }
}
