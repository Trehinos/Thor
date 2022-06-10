<?php

namespace Thor\Structures\Biology;

abstract class Neuron
{

    /**
     * @var callable
     */
    private $activationFunction;

    /**
     * @var float|callable
     */
    private $fireFunction;

    private float $value = 0;

    /**
     * Construct the Neuron.
     *
     * The **activation function** alters `$this->value` when a value is fired as this Neuron. Its prototype is :
     *
     * ```php
     *
     * fn (float $this->value, float $value): float
     *
     * ```
     *
     * The **fire function** is called when the Neuron is activated. It is the value sent by this Neuron.
     *
     * ```php
     *
     * fn (float $overflow): float
     *
     * ```
     *
     * @param float          $limit
     * @param callable       $activationFunction
     * @param float|callable $fireFunction
     * @param float          $decreaseValue
     */
    public function __construct(
        private float $limit,
        callable $activationFunction,
        float|callable $fireFunction = 1.0,
        private float $decreaseValue = 0.0
    ) {
        $this->activationFunction = $activationFunction;
        if (is_float($this->fireFunction)) {
            $this->fireFunction = fn () => $fireFunction;
        }
    }

    protected function evaluate(float $value): bool
    {
        return ($this->value = ($this->activationFunction)($this->value, $value)) > $this->limit;
    }

    protected function afterActivation(float $overflow): bool
    {
        return true;
    }

    protected function decrease(): void
    {
        $this->value -= $this->decreaseValue;
    }

    public function fire(float $value): float
    {
        if ($this->evaluate($value)) {
            $overflow = $this->value - $this->limit;
            if ($this->afterActivation($overflow)) {
                $this->value = 0;
                return ($this->fireFunction)($overflow);
            }
        } else {
            $this->decrease();
        }

        return 0;
    }

}
