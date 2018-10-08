<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Helpers\Tests;

use OpenEngine\Mika\Core\Helpers\Path;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    public function testAbsolutePath(): void
    {
        Path::setRoot('/tmp');
        $this->assertStringEndsWith('/tmp/akmal', Path::get('akmal'));
    }

    public function testRoot(): void
    {
        Path::setRoot(__DIR__);
        $this->assertStringEndsWith(__DIR__, Path::getRoot());
    }

    public function testNames(): void
    {
        Path::setRoot('/tmp');
        Path::addName('src', 'src');

        $this->assertStringEndsWith('/tmp/src', Path::getName('src'));
    }
}
