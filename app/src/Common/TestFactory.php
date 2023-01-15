<?php

namespace Evolution\Common;

use Evolution\DataModel\City\City;
use Evolution\DataModel\Nation;
use Evolution\DataModel\Player;

final class TestFactory
{

    public static function createCity(): City
    {
        $city = new City('Ville de test');


        return $city;
    }

    public static function createPlayer(): Player
    {
        return new Player('Razelus', new Nation('Razie', cities: [self::createCity()]));
    }

}
