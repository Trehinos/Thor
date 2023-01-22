<?php

namespace Evolution\DataModel\Player;

use Evolution\DataModel\City\City;

class Nation
{

    /**
     * @param string $name
     * @param int $food
     * @param int $culture
     * @param int $purse
     * @param array $armies
     * @param City[] $cities
     */
    public function __construct(
        public string $name,
        public int $food = 0,
        public int $culture = 0,
        public int $purse = 0,
        public array $armies = [],
        public array $cities = [],
    ) {}

    public function production(): int
    {
        return 0;
    }

    public function health(): float
    {
        return 100.0;
    }

}
