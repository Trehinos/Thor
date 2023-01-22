<?php

namespace Evolution\DataModel\Resource;

use Evolution\Common\Units;

class Resource
{

    public Unit $unit;

    public const PRIMARY = 0x01;
    public const COLLECTABLE = 0x02;
    public const PRODUCT = 0x04;

    /**
     * @param string $name
     * @param Unit|null $unit
     * @param string $icon
     * @param string $color
     * @param int $flags
     */
    public function __construct(
        public readonly string $name,
        ?Unit                  $unit = null,
        public readonly string $icon = 'square',
        public readonly string $color = '#fff',
        public readonly int    $flags = 0
    ) {
        $this->unit = $unit ?? Units::unit();
    }

    public function is(int $flag): bool
    {
        return 0 !== ($flag & $this->flags);
    }

}
