<?php

namespace Thor\Message\Part;

use Thor\Message\Headers\Headers;

final class EmptyPart extends Part
{

    public function __construct()
    {
        parent::__construct('text', 'plain');
        $this->headers = new Headers();
    }

    public function getBody(): string
    {
        return '';
    }

}
