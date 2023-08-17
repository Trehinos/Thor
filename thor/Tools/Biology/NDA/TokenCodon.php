<?php

namespace Thor\Tools\Biology\NDA;

class TokenCodon extends BaseCodon
{

    public function __construct(string $nda, public readonly string $textToken)
    {
        parent::__construct($nda);
    }

    public function getRaw(): string
    {
        return $this->textToken;
    }

    public static function startCodon(): self
    {
        return new self('00A', 'START');
    }


    public static function blockCreateNeuron(): self
    {
        return new self('S0A', 'CREATE NEURON');
    }
    public static function endBlock(): self
    {
        return new self('SZZ', 'END BLOCK');
    }

    public static function endCodon(): self
    {
        return new self('00H', 'END');
    }
}
