<?php

namespace Thor\Database;

trait PdoRowTrait
{

    private ?int $id = null;

    private ?string $public_id = null;

    /**
     * @return int
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
     * @return string
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

    public function generatePublicId()
    {
        $this->public_id = bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(4)) .
            '-' . bin2hex(random_bytes(4));
    }


}
