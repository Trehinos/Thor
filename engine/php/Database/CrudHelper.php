<?php

namespace Thor\Database;

final class CrudHelper
{

    private PdoRequester $requester;
    private string $className;

    public function __construct(string $className, PdoRequester $requester)
    {
        $this->requester = $requester;
        $this->className = $className;
    }

    public function table(): string
    {
        return strtolower(substr($this->className, strrpos($this->className, '\\') + 1));
    }

    public function listAll(): array
    {
        $rows = $this->requester->request("SELECT * FROM {$this->table()}", [])->fetchAll();
        if (empty($rows)) {
            return [];
        }

        $rowsObjs = [];
        foreach ($rows as $row) {
            $className = $this->className;
            $rowObj = new $className();
            $rowObj->fromPdoArray($row);
            $rowsObjs[] = $rowObj;
        }

        return $rowsObjs;
    }

    /**
     * @param PdoRowInterface $row
     * @return string the public_id
     */
    public function createOne(PdoRowInterface $row): string
    {
        $row->generatePublicId();
        $pdoArray = $row->toPdoArray();
        unset($pdoArray['id']);

        $columns = implode(', ', array_keys($pdoArray));
        $values = implode(', ', array_fill(0, count($pdoArray), '?'));

        $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($values)", array_values($pdoArray));

        return $pdoArray['public_id'];
    }

    public function readOne(string $id): ?PdoRowInterface
    {
        $row = $this->requester->request("SELECT * FROM {$this->table()} WHERE id = ?", [$id])->fetchAll()[0] ?? [];
        if (empty($row)) {
            return null;
        }

        $className = $this->className;
        $rowObj = new $className();
        $rowObj->fromPdoArray($row);

        return $rowObj;
    }

    public function readOneFromPid(string $pid): ?PdoRowInterface
    {
        $row = $this->requester->request("SELECT * FROM {$this->table()} WHERE public_id = ?", [$pid])->fetchAll()[0] ?? [];
        if (empty($row)) {
            return null;
        }

        $className = $this->className;
        $rowObj = new $className();
        $rowObj->fromPdoArray($row);

        return $rowObj;
    }

    public function updateOne(PdoRowInterface $row)
    {
        $pdoArray = $row->toPdoArray();
        $sets = implode(', ', array_map(fn(string $col) => "$col = ?", array_keys($pdoArray)));

        $this->requester->execute(
            "UPDATE {$this->table()} SET $sets WHERE id=?",
            [array_values($pdoArray), $row->getId()]
        );
    }

    public function deleteOne(PdoRowInterface $row)
    {
        $this->requester->execute("DELETE FROM {$this->table()} WHERE id=?", [$row->getId()]);
    }

}
