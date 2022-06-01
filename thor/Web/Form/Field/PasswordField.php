<?php

/**
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Web\Form\Field;

/**
 *
 */

/**
 *
 */
class PasswordField extends InputField
{

    /**
     * @param string      $name
     * @param bool        $readOnly
     * @param bool        $required
     * @param string|null $htmlClass
     */
    public function __construct(string $name, bool $readOnly = false, bool $required = false, ?string $htmlClass = null)
    {
        parent::__construct($name, 'password', $readOnly, $required, $htmlClass);
    }

}
