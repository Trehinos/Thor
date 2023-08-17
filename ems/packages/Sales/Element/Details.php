<?php

namespace Ems\Packages\Sales\Element;

readonly class Details
{
    public function __construct(
        public int $id,
        public string $corporateName,
        public string $phone,
        public string $mobile,
        public string $fax,
        public string $email,
        public string $website,
    ) {
    }

}
