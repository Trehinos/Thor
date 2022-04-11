<?php

namespace Thor\Tools\Spreadsheet;

use Thor\Structures\Collection\Collection;

class StyleCollection extends Collection
{

    /**
     * @param StyleApplier[]
     */
    public function __construct(array $styles = [])
    {
        parent::__construct($styles);
    }

    public function addStyle(string $name, StyleApplier $style): static
    {
        $this[$name] = $style;
        return $this;
    }

    public function getStyle(string $name): ?Style
    {
        return $this[$name] ?? $this[$this->keyFirst()] ?? null;
    }

}
