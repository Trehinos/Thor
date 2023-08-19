<?php

namespace Ems\Packages\Sales\Element;

readonly class Contact
{

    public function __construct(
        public Details $details,
        public Address $billingAddress,
        public ?Address $deliveryAddress = null,
    ) {}

}
