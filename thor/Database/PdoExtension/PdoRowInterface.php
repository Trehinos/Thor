<?php

namespace Thor\Database\PdoExtension;

interface PdoRowInterface
{

    // <-> SQL Methods

    public function getPdoColumnsDefinitions(): array;

    public function getTableName(): string;

    public function toPdoArray(): array;

    public function fromPdoArray(array $pdoArray): void;


    // DEFAULT ACCESSORS & METHODS

    public function getId(): ?int;

    public function getPublicId(): ?string;

    public function generatePublicId(): void;

}
