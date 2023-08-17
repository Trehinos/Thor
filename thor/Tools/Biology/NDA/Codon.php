<?php

namespace Thor\Tools\Biology\NDA;

abstract class Codon
{

    /**
     * @param Nucleotide[] $nucleotides
     */
    protected function __construct(public array $nucleotides) {}

    public function getRaw(): string
    {
        return '';
    }

}
