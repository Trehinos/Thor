<?php

namespace Thor\Tools\Email;

use Thor\Tools\Strings;

class Part
{

    public function __construct(
        public Headers $headers = new Headers(),
        public string $body = ''
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
        $name = basename($path);
        $headers = new Headers(
            Strings::interpolate(Headers::TYPE_OCTET_STREAM, ['name' => $name]),
            'base64'
        );
        $headers['Content-Disposition'] = "attachment; filename=\"$name\"";
        return new self(
            $headers,
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