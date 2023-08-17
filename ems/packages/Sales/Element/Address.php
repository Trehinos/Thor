<?php

namespace Ems\Packages\Sales\Element;

readonly class Address
{

    public function __construct(
        public int $id,
        public string $title,
        public string $address1,
        public string $address2,
        public string $address3,
        public string $zip,
        public string $city,
    ) {
    }

}
