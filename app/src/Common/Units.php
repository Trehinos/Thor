<?php

namespace Evolution\Common;

use Evolution\DataModel\Resource\Unit;

final class Units
{

    private static array $data = [];

    public static function unit(): Unit
    {
        return self::$data[''] ??= new Unit('', 1000, ['', 'K', 'M', 'G'], 0);
    }

    public static function weight(): Unit
    {
        return self::$data['g'] ??= new Unit('g', 1000, ['g', 'kg', 'T', 'KT', 'MT']);
    }

    public static function smallWeight(): Unit
    {
        return self::$data['mg'] ??= new Unit('mg', 1000, ['mg', 'g', 'kg', 'T']);
    }

    public static function percent(): Unit
    {
        return self::$data['%'] ??= new Unit('%', digits: 2);
    }

    public static function surface(): Unit
    {
        return self::$data['surface'] ??= new Unit('m²', 1000000, ['m²', 'km²']);
    }

    public static function volume(): Unit
    {
        return self::$data['volume'] ??= new Unit('cl', 100, ['cl', 'l', 'hl']);
    }

    public static function power(): Unit
    {
        return self::$data['w'] ??= new Unit('W', 1000, ['W', 'KW', 'MW', 'GW'], 0);
    }

}
