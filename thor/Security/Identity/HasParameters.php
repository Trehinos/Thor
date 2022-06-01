<?php

namespace Thor\Security\Identity;

/**
 *
 */

/**
 *
 */
interface HasParameters
{
    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function setParameter(string $key, mixed $value): void;

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getParameter(string $key): mixed;

    /**
     * @return array
     */
    public function getParameters(): array;
}
