<?php

namespace Thor\Tools\Biology\NDA;

abstract class BaseCodon extends Codon
{

    public function __construct(string $nda)
    {
        parent::__construct(Nucleotide::nucleotidesFromString($nda));
    }

}
