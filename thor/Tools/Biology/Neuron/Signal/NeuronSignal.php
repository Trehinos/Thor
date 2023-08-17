<?php

namespace Thor\Tools\Biology\Neuron\Signal;

interface NeuronSignal
{

    public function neuronValue(): NeuronValue;

    public function get(): mixed;

}
