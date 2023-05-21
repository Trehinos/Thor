<?php

/**
 * @package          Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Http\Web\Form\Field;

/**
 *
 */

/**
 *
 */
class InputField extends AbstractField
{

    /**
     * @param string      $name
     * @param string      $inputType
     * @param bool        $read_only
     * @param bool        $required
     * @param string|null $htmlClass
     * @param string|null $value
     */
    public function __construct(
        string $name,
        protected string $inputType,
        bool $read_only = false,
        bool $required = false,
        ?string $htmlClass = null,
        ?string $value = null
    ) {
        parent::__construct('input', $name, $value ?? '', $read_only, $required, $htmlClass);
        $this->setAttribute('type', $this->inputType);
    }

}
