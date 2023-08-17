<?php

namespace Ems\Packages\Sales\Currencies;

use Ems\Packages\Sales\Element\Currency;

final readonly class Euro extends Currency {
    public function __construct()
    {
        parent::__construct('euro', '€');
    }
}
