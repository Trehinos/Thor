<?php

namespace Thor\Tools\Biology\Neuron;

use Thor\Tools\Biology\Neuron\Signal\NeuronSignal;

interface FormalNeuron
{

    /**
     * @return NeuronSignal[]
     */
    function getInputs(): array;
    function activate(): NeuronSignal;

}
