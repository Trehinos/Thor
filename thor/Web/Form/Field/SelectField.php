<?php

/**
 * @package          Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Web\Form\Field;

use Thor\Web\Node;
use Thor\Web\TextNode;

/**
 *
 */

/**
 *
 */
class SelectField extends AbstractField
{

    /**
     * @param array       $options ['label' => 'value']
     */
    public function __construct(
        string $name,
        bool $multiple,
        array $options = [],
        bool $readOnly = false,
        bool $required = false,
        ?string $htmlClass = null,
        ?string $value = null
    ) {
        parent::__construct('select', $name, null, $readOnly, $required, $htmlClass);
        $this->setAttribute('multiple', $multiple);
        foreach ($options as $label => $optionValue) {
            $option = new Node('option', $this);
            $option->setAttribute('value', $optionValue);
            if ($value !== null && $optionValue === $value) {
                $option->setAttribute('selected', true);
            }
            $option->addChild(new TextNode($label));
            $this->addChild($option);
        }
    }

}
