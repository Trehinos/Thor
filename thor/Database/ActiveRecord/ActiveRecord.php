<?php

namespace Thor\Database\ActiveRecord;

use Thor\Database\Criteria;
use Thor\Database\PdoExtension\PdoRequester;

abstract class ActiveRecord implements ActiveInterface, RecordInterface
{

    private static PdoRequester $requester;
    private static string $tableName;

    private static array $primaryKeys;

    private array $dbData = [];

    private bool $synced = false;

    public function __construct(private array $currentData)
    {
    }

    public function isSynced(): bool
    {
        return $this->synced;
    }

    public function reset(): void
    {
        $this->synced = true;
        $this->currentData = $this->dbData;
    }

    public function save(): void
    {
    }

    public static function load(Criteria $criteria): static
    {
        // TODO: Implement load() method.
    }

    public function pk(): array
    {
        // TODO: Implement pk() method.
    }

    public function index(string $index): array
    {
        // TODO: Implement index() method.
    }

    public function fk(array $keyValue): mixed
    {
        // TODO: Implement fk() method.
    }

    public function __get(string $name): mixed
    {
        return $this->currentData[$name] ?? null;
    }

    public function __set(string $name, mixed $value = null): void
    {
        $this->currentData[$name] = $value;
    }

}
