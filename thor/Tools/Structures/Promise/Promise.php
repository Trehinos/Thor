<?php

namespace Thor\Tools\Structures\Promise;

use Exception;

/**
 *
 */

/**
 *
 */
class Promise
{

    private PromiseState $state = PromiseState::PENDING;

    /**
     * @param mixed|null $value
     */
    public function __construct(private readonly mixed $value = null)
    {
    }

    /**
     * @return PromiseState
     */
    public function getState(): PromiseState
    {
        return $this->state;
    }

    /**
     * @param callable|null $onFulfillment
     * @param callable|null $onRejection
     *
     * @return $this
     */
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
