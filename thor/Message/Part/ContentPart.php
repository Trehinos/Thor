<?php

namespace Thor\Message\Part;

use Thor\Message\Headers\Headers;

class ContentPart extends Part
{
    public function __construct(
        public readonly string $body
    ) {
        parent::__construct('', '');
        $this->headers = new Headers();
    }

    public function getBody(): string
    {
        return $this->body;
    }

}
