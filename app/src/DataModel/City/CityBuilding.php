<?php

namespace Evolution\DataModel\City;

use Evolution\DataModel\Building\Building;

class CityBuilding
{

    public function __construct(
        public readonly City     $city,
        public readonly Building $building,
        public int               $posX,
        public int               $posY,
        public int               $built = 0,
        public int               $currentWorkers = 0
    ) {
    }

}
