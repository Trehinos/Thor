<?php

namespace Thor\Database\PdoTable\PdoRecord;

use Thor\Database\PdoTable\Criteria;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoTable\CrudInterface;
use Thor\Database\PdoTable\PdoRow\PdoRowInterface;
use Thor\Database\PdoTable\SchemaHelper;

/**
 * @implements PdoRecord
 * @implements PdoRowInterface
 */
trait PdoRecordTrait
{

    protected bool $isSynced = false;
    protected bool $exists = false;
    protected bool $empty = true;

    public function __construct(private readonly CrudInterface $crudHelper, private readonly SchemaHelper $schemaHelper)
    {
    }

    final public function getCrudHelper(): CrudHelper
    {
        return $this->crudHelper;
    }

    final public function getSchemaHelper(): SchemaHelper
    {
        return $this->schemaHelper;
    }

    public function createTable(): bool
    {
        return $this->schemaHelper->createTable();
    }

    public function synced(): ?bool
    {
        return !$this->empty ? $this->exists && $this->isSynced : null;
    }

    public function insert(): bool
    {
        if ($this->synced()) {
            return true;
        }
        $ret = $this->getCrudHelper()->createOne($this);
        if ($ret) {
            $this->isSynced = true;
            $this->exists = true;
        }
        return $ret;
    }

    public function update(): bool
    {
        if ($this->synced()) {
            return true;
        }
        $ret = $this->getCrudHelper()->updateOne($this);
        if ($ret) {
            $this->isSynced = true;
            $this->exists = true;
        }
        return $ret;
    }

    public function upsert(): bool
    {
        return match ($this->synced()) {
            true, false => $this->update(),
            null => !$this->empty && $this->insert()
        };
    }

    public function delete(): bool
    {
        if ($this->isSynced) {
            return $this->crudHelper->deleteOne($this);
        }
        return false;
    }

    public function reload(?Criteria $criteria = null): bool
    {
        if ($criteria === null) {
            if ($this->empty) {
                return false;
            }
            if ($this->isSynced) {
                return true;
            }
            $criteria = $this->crudHelper->primaryArrayToCriteria($this->getPrimary());
        }
        $r = $this->crudHelper->readOneBy($criteria);
        if ($r === null) {
            $this->isSynced = false;
            return false;
        }
        $this->fromPdoArray($r->toPdoArray(), true);
        $this->isSynced = true;
        return true;
    }

    public function dropTable(): bool
    {
        return $this->schemaHelper->dropTable();
    }
}
