<?php

namespace Thor\Tools\Structures;

use Stringable;

class EncodedString implements Stringable
{

    private string $encoding;

    public function __construct(
        private string $value = '',
        ?string $encoding = null
    ) {
        $this->encoding = $encoding ?? mb_detect_encoding($this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function setEncoding(string $encoding): void
    {
        $this->value = $this->recode($encoding);
        $this->encoding = $encoding;
    }

    public function recode(string $to): string
    {
        return mb_convert_encoding($this->value, $to, $this->encoding);
    }

}
