<?php

namespace Thor\Database\PdoTable\PdoRecord;

use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoTable\Criteria;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoTable\Driver\DriverInterface;
use Thor\Database\PdoTable\SchemaHelper;
use Thor\Database\PdoTable\PdoRow\PdoRowInterface;

/**
 * @implements PdoRecordInterface
 * @implements PdoRowInterface
 */
trait PdoRecordTrait
{

    protected bool $isSynced = false;
    protected bool $existsInDatabase = false;
    protected bool $objectEmpty = true;

    public function __construct(private readonly RecordManager $manager)
    {
    }

    public static function load(DriverInterface $driver, PdoRequester $requester, mixed ...$args): static
    {
        return new static(RecordManager::create($driver, $requester, static::class), ...$args);
    }

    final public function getCrudHelper(): CrudHelper
    {
        return $this->manager->crud;
    }

    final public function getSchemaHelper(): SchemaHelper
    {
        return $this->manager->schema;
    }

    public function createTable(): bool
    {
        return $this->getSchemaHelper()->createTable();
    }

    public function synced(): ?bool
    {
        return !$this->objectEmpty ? $this->existsInDatabase && $this->isSynced : null;
    }

    public function insert(): bool
    {
        if ($this->objectEmpty || $this->existsInDatabase) {
            return false;
        }
        $this->isSynced = false;
        $ret = $this->getCrudHelper()->createOne($this);
        if ($ret) {
            $this->isSynced = true;
            $this->existsInDatabase = true;
        }
        return $ret;
    }

    public function update(): bool
    {
        if ($this->objectEmpty || !$this->existsInDatabase) {
            return false;
        }
        $this->isSynced = false;
        $ret = $this->getCrudHelper()->updateOne($this);
        if ($ret) {
            $this->isSynced = true;
        }
        return $ret;
    }

    public function upsert(): bool
    {
        return match ($this->existsInDatabase) {
            true => $this->update(),
            false => $this->insert()
        };
    }

    public function delete(): bool
    {
        if ($this->existsInDatabase) {
            $this->isSynced = false;
            return $this->getCrudHelper()->deleteOne($this);
        }
        return false;
    }

    public function reload(?Criteria $criteria = null): bool
    {
        if ($criteria === null) {
            if ($this->isSynced) {
                return true;
            }
            $criteria = $this->getCrudHelper()->primaryArrayToCriteria($this->getPrimary());
        }
        $r = $this->getCrudHelper()->readOneBy($criteria);
        if ($r === null) {
            $this->existsInDatabase = false;
            $this->isSynced = false;
            return false;
        }
        $this->fromPdoArray($r->toPdoArray(), true);
        $this->isSynced = true;
        $this->existsInDatabase = true;
        return true;
    }

    public function dropTable(): bool
    {
        return $this->getSchemaHelper()->dropTable();
    }
}
