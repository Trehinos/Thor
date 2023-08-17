<?php

namespace Ems\Types;

use Stringable;

interface AmountInterface extends Stringable
{

    public function toFloat(): float;

}
