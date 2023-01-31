<?php

namespace Evolution\DataModel\City;

use Evolution\DataModel\Player\Player;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoColumn;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoTable;
use Thor\Database\PdoTable\TableType\IntegerType;

#[PdoTable('city', ['id'], 'id')]
#[PdoColumn('id', new IntegerType(), false)]
class City
{

    /**
     * @param string $name
     * @param Player|null $owner
     * @param int $wealth
     * @param int $food
     * @param int $population
     * @param CityBuilding[] $buildings
     */
    public function __construct(
        public string $name,
        public ?Player $owner = null,
        public int    $wealth = 0,
        public int    $food = 0,
        public int    $population = 0,
        public array  $buildings = []
    ) {
    }

    public function workers(): int
    {
        return array_reduce(
            $this->buildings,
            fn (CityBuilding $building) => $building->built < 1.0
                ? 0
                : $building->currentWorkers
        );
    }

    public function builders(): int
    {
        return array_reduce(
            $this->buildings,
            fn (CityBuilding $building) => $building->built < 1.0
                ? $building->currentWorkers
                : 0
        );
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
