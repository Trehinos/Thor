<?php

/**
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Html\TableMatrix;

class MatrixColumn
{

    private $fromDatabase;
    private $toDatabase;

    /**
     * MatrixColumn constructor.
     *
     * @param string        $label
     * @param string        $htmlClass
     * @param callable|null $fromDatabase fn (string): string
     * @param callable|null $toDatabase   fn (string): string
     */
    public function __construct(
        public string $label,
        public string $htmlClass = '',
        ?callable $fromDatabase = null,
        ?callable $toDatabase = null,
    ) {
        $this->fromDatabase = $fromDatabase;
        $this->toDatabase = $toDatabase;
    }

    public function fromDb(string $dbValue): string
    {
        if (null === $this->fromDatabase) {
            return $dbValue;
        }
        return ($this->fromDatabase)($dbValue);
    }

    public function toDb(string $phpValue): string
    {
        if (null === $this->toDatabase) {
            return $phpValue;
        }
        return ($this->toDatabase)($phpValue);
    }

}
