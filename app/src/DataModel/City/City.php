<?php

namespace Evolution\DataModel\City;

class City
{

    /**
     * @param string $name
     * @param int $wealth
     * @param int $food
     * @param int $population
     * @param CityBuilding[] $buildings
     */
    public function __construct(
        public string $name,
        public int    $wealth = 0,
        public int    $food = 0,
        public int    $population = 0,
        public array  $buildings = []
    ) {
    }

    public function workers(): int
    {
        return 0;
    }

    public function builders(): int
    {
        return 0;
    }

    public function fighters(): int
    {
        return 0;
    }

    public function maxFighters(): int
    {
        return 0;
    }

}
