<?php

namespace Ems\Packages\Sales\Piece;

use Ems\Packages\Sales\Element\Item;
use Ems\Types\Amount;
use Ems\Types\Rate;

class Line
{

    public function __construct(
        public int $number,
        public Item $item,
        public int $quantity,
        public ?Rate $discount = null,
    ) {
    }

    public function getAmount(): Amount
    {
        return new Amount($this->item->price, $this->quantity, $this->discount);
    }

}
