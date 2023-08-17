<?php

namespace Thor\Tools\Biology\NDA;

class ValueCodon extends BaseCodon
{

    public function __construct(protected readonly string $nda)
    {
        parent::__construct($nda);
    }

    public function getRaw(): string
    {
        return Nucleotide::stringToInt($this->nda) . '';
    }
}
