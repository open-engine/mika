<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Components\Http\Message\Stream;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    /**
     * @var resource
     */
    private $stream;

    /**
     * Stream constructor.
     * @param $stream
     */
    public function __construct($stream)
    {
        if (!\is_resource($stream)) {
            throw new \InvalidArgumentException('Stream must be a resource');
        }

        $this->stream = $stream;
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString(): string
    {
        try {
            $this->seek(0);
            return (string)stream_get_contents($this->stream);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close(): void
    {
        if (\is_resource($this->stream)) {
            \fclose($this->stream);
        }

        $this->detach();
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        if ($this->stream === null) {
            return null;
        }

        $result = $this->stream;

        $this->stream = null;

        return $result;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize(): ?int
    {
        if ($this->stream === null) {
            return null;
        }

        $stats = \fstat($this->stream);

        return $stats['size'] ?? null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell(): int
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Stream is detached');
        }

        $result = \ftell($this->stream);

        if ($result === false) {
            throw new \RuntimeException('Unable to determine stream position');
        }

        return $result;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof(): bool
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Stream is detached');
        }

        return \feof($this->stream);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable(): bool
    {
        $seekable = $this->getMetadata('seekable');

        if ($seekable) {
            return (bool)$seekable;
        }

        return $seekable ? (bool)$seekable : false;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Can not seek, stream is detached');
        }

        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException('Can not seek');
        }
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind(): void
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Can not rewind. Stream is detached');
        }

        if (!rewind($this->stream)) {
            throw new \RuntimeException('Can not rewind');
        }
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        /** @var string $mode */ 
        $mode = $this->getMetadata('mode');

        if ($mode === null) {
            return false;
        }

        switch (strtolower($mode)) {
            case 'r+':
            case 'w':
            case 'wb':
            case 'w+':
            case 'wb+':
            case 'a':
            case 'ab':
            case 'a+':
            case 'ab+':
            case 'x':
            case 'xb':
            case 'x+':
            case 'xb+':
            case 'c':
            case 'cb':
            case 'c+':
            case 'cb+':
                return true;
            default:
                return false;
        }
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string): int
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Can not write, stream is detached');
        }

        $res = \fwrite($this->stream, $string);

        if (\is_int($res)) {
            return $res;
        }

        throw new \RuntimeException('Error on writing file');
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        $mode = $this->getMetadata('mode');

        if ($mode === null) {
            return false;
        }

        switch (strtolower($mode)) {
            case 'r':
            case 'r+':
            case 'rb+':
            case 'a+':
            case 'ab+':
            case 'x+':
            case 'xb+':
            case 'c+':
            case 'cb+':
                return true;
            default:
                return false;
        }
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length): string
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Can not read, stream is detached');
        }

        $result = \fread($this->stream, $length);

        return (string)$result;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents(): string
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Can not get contents, stream is detached');
        }

        $result = stream_get_contents($this->stream);

        if (\is_string($result)) {
            return $result;
        }

        throw new \RuntimeException('Can not read a stream');
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if ($this->stream === null) {
            return null;
        }

        $meta = stream_get_meta_data($this->stream);

        if ($key === null) {
            return $meta;
        }

        if (isset($meta[$key])) {
            return $meta[$key];
        }

        return null;
    }
}
