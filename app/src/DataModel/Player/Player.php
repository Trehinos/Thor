<?php

namespace Evolution\DataModel\Player;

class Player
{

    public function __construct(
        public string $name,
        public Nation $nation,
    ) {}

}
