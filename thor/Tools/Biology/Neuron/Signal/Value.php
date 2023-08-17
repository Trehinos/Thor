<?php

namespace Thor\Tools\Biology\Neuron\Signal;

class Value implements NeuronValue
{

    public function __construct(private int $numerator , private int $denominator = 1)
    {
    }

    public function multiply(int $n = 2): self
    {
        if ($this->denominator % $n === 0) {
            $this->denominator /= $n;
            return $this;
        }
        $this->numerator *= $n;
        return $this;
    }

    public function divide(int $n = 2): self
    {
        if ($this->numerator % $n === 0) {
            $this->numerator /= $n;
        }
        $this->denominator *= $n;
        return $this;
    }

    public function get(): float
    {
        return floatval($this->numerator) / floatval($this->denominator);
    }

}
