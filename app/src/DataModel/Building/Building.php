<?php

namespace Evolution\DataModel\Building;

class Building
{

    public function __construct(
        public string $name,
        public int    $buildTime,
        public array  $costs = [],

        public int    $sizeX = 1,
        public int    $sizeY = 1,

        public int    $maxWorkers = 0,
        public array  $recipes = []


    ) {
    }

}
