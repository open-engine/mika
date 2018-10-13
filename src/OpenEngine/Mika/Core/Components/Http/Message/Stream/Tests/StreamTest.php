<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Http\Message\Stream\Tests;

use OpenEngine\Mika\Core\Components\Http\Message\Stream\StreamFactory;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    private const FILE = __DIR__ . '/Dummy/temp.text';

    public function testCreate(): void
    {
        $stream = (new StreamFactory)->createStream('Testing streams');
        self::assertStringEndsWith('Testing streams', $stream->__toString());
        $stream->close();
    }

    public function testModes(): void
    {
        $stream = (new StreamFactory)->createStreamFromFile(self::FILE);
        self::assertFalse($stream->isWritable());
        $stream->close();
    }

    public function testModes2(): void
    {
        $stream = (new StreamFactory)->createStreamFromFile(self::FILE, 'w');
        self::assertTrue($stream->isWritable());
        self::assertFalse($stream->isReadable());
        $stream->close();
    }

    public function testWriteAndReadFile(): void
    {
        $stream = (new StreamFactory)->createStreamFromFile(self::FILE, 'w+');

        self::assertEquals(10, $stream->write('It is test'));

        $stream->rewind();

        self::assertStringEndsWith('It', $stream->read(2));
        $stream->close();
    }

    public function testCreateByResource(): void
    {
        $res = fopen(self::FILE, 'w+');
        $stream = (new StreamFactory)->createStreamFromResource($res);

        self::assertEquals(9, $stream->write('Framework'));
        self::assertStringEndsWith('Framework', $stream->__toString());

        $stream->close();
    }
}
