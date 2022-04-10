<?php

namespace Thor\Structures\Promise;

use Exception;

class Promise
{

    private PromiseState $state = PromiseState::PENDING;

    public function __construct(private mixed $value = null)
    {
    }

    public function getState(): PromiseState
    {
        return $this->state;
    }

    public function then(?callable $onFulfillment = null, callable $onRejection = null): static
    {
        if (($onFulfillment === null && $onRejection === null) || $this->state !== PromiseState::PENDING) {
            return $this;
        }

        try {
            $promise = new static($onFulfillment($this->value));
            $this->state = PromiseState::FULFILLED;
        } catch (Exception $e) {
            $promise = new static($onRejection($e));
            $this->state = PromiseState::REJECTED;
        }
        return $promise;
    }

    /**
     * @return mixed|void
     * @throws Exception
     */
    public function wait(bool $unwrap = true)
    {
        if ($unwrap) {
            if ($this->value instanceof Exception) {
                throw $this->value;
            }
            return $this->value;
        }
    }

}
