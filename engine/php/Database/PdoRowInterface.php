<?php

namespace Thor\Database;

interface PdoRowInterface
{

    public function getId(): ?int;

    public function getPublicId(): ?string;

    public function generatePublicId();

    public function toPdoArray(): array;

    public function fromPdoArray(array $pdoArray);

}
