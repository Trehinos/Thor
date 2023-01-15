<?php

namespace Evolution\DataModel;

class Player
{

    public function __construct(
        public string $name,
        public Nation $nation,
    ) {}

}
