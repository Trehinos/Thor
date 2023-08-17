<?php

namespace Thor\Tools\Biology\Neuron\Signal;

class Signal implements NeuronSignal
{

    /**
     * @var NeuronValue[]
     */
    private array $values = [];

    /**
     * @param NeuronValue|NeuronValue[]|null $init
     */
    public function __construct(NeuronValue|array|null $init = null)
    {
        if ($init !== null) {
            $this->values = is_array($init) ? $init : [$init];
        }
    }

    public function addValue(NeuronValue $v): void
    {
        $this->values[] = $v;
    }

    public function neuronValue(): NeuronValue
    {
        $sum = 0.0;
        foreach ($this->values as $value) {
            $sum += $value->get();
        }
        return new Value($sum, count($this->values));
    }

    public function get(): mixed
    {
        return $this->neuronValue()->get();
    }
}
