<?php

namespace Evolution\DataModel\Player;

use Evolution\DataModel\Resource\Count;

class Advance
{

    /**
     * @param string $name
     * @param string $description
     * @param Count[] $costs
     * @param Unlock[] $unlocks
     * @param string[] $predicates
     */
    public function __construct(
        public string $name,
        public string $description = '',
        public array  $costs = [],
        public array  $unlocks = [],
        public array  $predicates = []
    ) {
    }


}
