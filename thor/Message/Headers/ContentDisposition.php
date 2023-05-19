<?php

namespace Thor\Message\Headers;

use Thor\Common\Types\Strings;

enum ContentDisposition: string
{

    case INLINE = 'inline';
    case ATTACHMENT = 'attachment; filename="{filename}"';

    public function get(array $context = []): string
    {
        return Strings::interpolate($this->value, $context);
    }

}
