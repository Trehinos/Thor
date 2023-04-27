<?php

namespace Thor\Message\Part;

use Thor\Message\Headers\Headers;
use Thor\Message\Headers\ContentTransferEncoding;

abstract class Part
{

    public function __construct(
        public readonly string $mediaType,
        public readonly string $mediaSubType,
        public readonly array|string|null $mediaParameter = null,
        public readonly ?ContentTransferEncoding $encoding = ContentTransferEncoding::BASE64,
        public ?Headers $headers = null
    ) {
        $this->headers ??= new Headers();
        $parameter = '';
        if (!empty($this->mediaParameter)) {
            $pad = str_repeat(' ', strlen('Content-Type'));
            $parameters = is_string($this->mediaParameter) ? [$this->mediaParameter] : $this->mediaParameter;
            foreach ($parameters as $p) {
                $parameter .= "\r\n$pad; $p";
            }
        }
        if ($this->encoding !== null) {
            $this->headers['Content-Transfer-Encoding'] = $this->encoding->value;
        }
        $this->headers['Content-Type'] = "{$this->getMimeType()}$parameter";
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    public function getMimeType(): string
    {
        return "{$this->mediaType}/{$this->mediaSubType}";
    }

    abstract public function getBody(): string;

    public function __toString(): string
    {
        $body = $this->chunk($this->getBody());
        return "{$this->getHeaders()}\r\n$body\r\n";
    }

    public function chunk(string $text): string
    {
        if ($this->encoding === null) {
            return $text;
        }
        return $this->encoding->encode($text);
    }
}
