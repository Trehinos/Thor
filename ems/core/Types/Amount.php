<?php

namespace Ems\Types;

readonly class Amount extends Price
{

    public function __construct(
        private Price $price,
        private int $quantity = 1,
        private ?Rate $discount = null
    ) {
        $amount = $this->price->toFloat() * $this->quantity;
        if ($this->discount) {
            $amount = $amount - $this->discount->alter($amount);
        }
        parent::__construct(
            $i = intval($amount),
            intval(($amount - $i) * pow(10, $this->price->precision)),
            $this->price->unit,
            $this->price->precision,
            $this->price->separator
        );
    }

}
