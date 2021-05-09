<?php

/**
 * @package Trehinos/Thor/Database/PdoExtension
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoExtension;

final class PdoTransaction extends PdoRequester
{

    public function __construct(PdoHandler $handler, private bool $autoTransaction = true)
    {
        parent::__construct($handler);
        if ($this->autoTransaction && !$this->handler->getPdo()->inTransaction()) {
            $this->begin();
        }
    }

    public function __destruct()
    {
        if ($this->handler->getPdo()->inTransaction()) {
            if ($this->autoTransaction) {
                $this->commit();
            } else {
                $this->rollback();
            }
        }
    }

    public function begin(): void
    {
        $this->handler->getPdo()->beginTransaction();
    }

    public function commit(): void
    {
        $this->handler->getPdo()->commit();
    }

    public function rollback(): void
    {
        $this->handler->getPdo()->rollBack();
    }

}
