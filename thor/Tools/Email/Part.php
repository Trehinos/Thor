<?php

namespace Thor\Tools\Email;

use Thor\Tools\Guid;
use Thor\Tools\Strings;

/**
 * This class represent an email part.
 */
class Part
{

    /** @var Part[] */
    protected array $parts = [];

    /**
     * Construct an email part with its headers.
     *
     * @param Headers $headers
     * @param string  $body
     */
    public function __construct(
        protected Headers $headers = new Headers(),
        protected string $body = '',
        protected string $boundary = ''
    ) {
    }

    /**
     * Adds an email part to this email.
     *
     * @param Part $part
     *
     * @return void
     */
    public function addPart(Part $part): void
    {
        $this->parts[] = $part;
    }

    /**
     * Returns the raw MIME message's body.
     *
     * @return string
     */
    public function getBody(): string
    {
        return "\r\n\r\n--$this->boundary\r\n" .
            implode(
                "\r\n\r\n--$this->boundary\r\n",
                array_map(
                    fn(Part $part) => "$part",
                    $this->parts
                )
            ) .
            "\r\n\r\n--$this->boundary--\r\n";
    }

    public function __toString(): string
    {
        return
            "{$this->headers}\r\n\r\n" .
            $this->body;
    }

    /**
     * Build a new part to attach a file on an email.
     *
     * @param string $path
     *
     * @return static
     */
    public static function file(string $path): self
    {
        return new self(
            Headers::fileAttachment(basename($path)),
            chunk_split(base64_encode(file_get_contents($path))),
        );
    }

    /**
     * Build a new part to inline a text in an email.
     *
     * @param string $text
     *
     * @return static
     */
    public static function text(string $text): self
    {
        return new self(
            new Headers(Headers::TYPE_HTML, 'base64'),
            chunk_split(base64_encode($text))
        );
    }

    /**
     * Build a new part to inline a text in an email.
     *
     * @param string $type
     *
     * @return static
     */
    public static function multipart(string $type): self
    {
        $b = Guid::base64();
        return new self(
            new Headers(Strings::interpolate(Headers::TYPE_MULTIPART, ['type' => $type, 'boundary' => $b]), 'base64', $b)
        );
    }

}
