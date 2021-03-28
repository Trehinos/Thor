<?php

namespace Thor\Html\PdoMatrix;


class MatrixColumn
{

    private $fromDatabase;
    private $toDatabase;

    /**
     * MatrixColumn constructor.
     *
     * @param string        $label
     * @param callable|null $fromDatabase fn (string): string
     * @param callable|null $toDatabase   fn (string): string
     * @param string        $htmlClass
     */
    public function __construct(
        public string $label,
        ?callable $fromDatabase = null,
        ?callable $toDatabase = null,
        public string $htmlClass = ''
    ) {
        $this->fromDatabase = $fromDatabase;
        $this->toDatabase = $toDatabase;
    }

    public function fromDb(string $dbValue): string
    {
        return ($this->fromDatabase)($dbValue);
    }

    public function toDb(string $phpValue): string
    {
        return ($this->toDatabase)($phpValue);
    }

}
