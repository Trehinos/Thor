<?php

namespace Evolution\DataModel\Player;

use Evolution\DataModel\Building\Building;
use Evolution\DataModel\Resource\Recipe;
use Evolution\DataModel\Resource\Resource;

class Unlock
{

    public function __construct(
        public readonly ?Resource $resource = null,
        public readonly ?Building $building = null,
        public readonly ?Recipe   $recipe = null,
        /* TODO
         *  * Unit
         *  * Feature
         */
        public readonly ?Unlock   $chain = null
    ) {
    }

}
