<?php

namespace Thor\Tools\Email;

/**
 * This class represent an email part.
 */
class Part
{

    /**
     * Construct an email part with its headers.
     *
     * @param Headers $headers
     * @param string  $body
     */
    public function __construct(
        protected Headers $headers = new Headers(),
        protected string $body = ''
    ) {
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

}