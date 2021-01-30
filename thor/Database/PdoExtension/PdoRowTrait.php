<?php

namespace Thor\Database\PdoExtension;

use Exception;

trait PdoRowTrait
{

    private ?int $id = null;

    private ?string $public_id = null;

    /**
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return ?string
     */
    public function getPublicId(): ?string
    {
        return $this->public_id;
    }

    /**
     * @param string $public_id
     */
    public function setPublicId(string $public_id): void
    {
        $this->public_id = $public_id;
    }

    /**
     * @throws Exception
     */
    public function generatePublicId(): void
    {
        $this->public_id = bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(4)) .
            '-' . bin2hex(random_bytes(4));
    }


    final public static function getPdoColumnsDefinitions(): array
    {
        return [
                'id' => 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'public_id' => 'VARCHAR(255) NOT NULL',
            ] + static::getTableColumns();
    }

    protected static function getTableColumns(): array
    {
        return [];
    }

    final public function toPdoArray(): array
    {
        return [
                'id' => $this->getId(),
                'public_id' => $this->getPublicId()
            ] + $this->toPdo();
    }

    abstract protected function toPdo(): array;

    final public function fromPdoArray(array $pdoArray): void
    {
        $this->setId($pdoArray['id']);
        $this->setPublicId($pdoArray['public_id']);
        $this->fromPdo($pdoArray);
    }

    abstract protected function fromPdo(array $pdoArray): void;

}
