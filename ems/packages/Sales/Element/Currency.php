<?php

namespace Ems\Packages\Sales\Element;

use Ems\Types\Price;

abstract readonly class Currency extends Unit
{
    final public function getSymbol(): string
    {
        return $this->abbr;
    }

    public function display(Price $p): string
    {
        return "$p {$this->getSymbol()}";
    }

}
