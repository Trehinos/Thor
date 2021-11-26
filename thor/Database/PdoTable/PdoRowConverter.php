<?php

/**
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoTable;

final class PdoRowConverter
{

    public function __construct(
        private PdoRowInterface $pdoRow
    ) {
    }

    public static function fromJson(string $className, string $json, mixed ...$constructorArguments): self
    {
        return new self(PdoRowTrait::instantiateFromRow($className, json_decode($json), false, ...$constructorArguments));
    }

    public static function fromArray(string $className, array $data, mixed ...$constructorArguments): self
    {
        return new self(PdoRowTrait::instantiateFromRow($className, $data, false, ...$constructorArguments));
    }

    public function get(): PdoRowInterface
    {
        return $this->pdoRow;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function toArray(): array
    {
        return $this->pdoRow->toPdoArray();
    }

}
