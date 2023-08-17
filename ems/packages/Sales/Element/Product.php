<?php

namespace Ems\Packages\Sales\Element;

use Ems\Company\ActivityType;
use Ems\Types\Price;

readonly class Product extends Item {
    public function __construct(
        string $code,
        string $label,
        string $description,
        Price $price = new Price(0),
        int $unity = 1,
        ?Unit $unit = null
    ) {
        parent::__construct($code, $label, $description, ActivityType::SALES, $price, $unity, $unit);
    }
}
