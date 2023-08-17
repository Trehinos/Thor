<?php

namespace Ems\Packages\Sales\Element;

use Ems\Types\Price;
use Ems\Company\ActivityType;

readonly class Item {

    public function __construct(
        public string $code,
        public string $label,
        public string $description,
        public ActivityType $activityType,
        public Price $price = new Price(0),
        public int $unity = 1,
        public ?Unit $unit = null
    ) {

    }

}
