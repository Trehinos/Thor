<?php

/**
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Html\Form;

class TextType extends InputType
{

    public function __construct(?string $pattern = null, bool $readOnly = false, bool $required = false, ?string $htmlClass = null)
    {
        parent::__construct('text', $readOnly, $required, $htmlClass);
        if (null !== $pattern) {
            $this->setAttr('pattern', $pattern);
        }

        $this->set('');
    }

}
