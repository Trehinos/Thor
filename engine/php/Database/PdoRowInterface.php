<?php

namespace Thor\Database;

interface PdoRowInterface
{

    // <-> SQL Methods

    public static function getPdoColumnsDefinitions(): array;

    public function toPdoArray(): array;

    public function fromPdoArray(array $pdoArray): void;


    // DEFAULT ACCESSORS & METHODS

    public function getId(): ?int;

    public function getPublicId(): ?string;

    public function generatePublicId(): void;

}
