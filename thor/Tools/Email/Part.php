<?php

namespace Thor\Tools\Email;

use Thor\Tools\Strings;

class Part
{

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

    public static function file(string $path): self
    {
        return new self(
            Headers::fileAttachment(basename($path)),
            chunk_split(base64_encode(file_get_contents($path))),
        );
    }

    public static function text(string $text): self
    {
        return new self(
            new Headers(Headers::TYPE_HTML, 'base64'),
            chunk_split(base64_encode($text))
        );
    }

}