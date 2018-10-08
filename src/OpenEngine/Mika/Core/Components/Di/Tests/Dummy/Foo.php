<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Di\Tests\Dummy;

class Foo implements FooInterface
{
    public function __construct(BarInterface $bar, Baz $baz)
    {
    }

    public function bar(BarInterface $bar, string $login): string
    {
        return 'Hello!';
    }
}
