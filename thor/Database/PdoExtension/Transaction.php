<?php

namespace Thor\Database\PdoExtension;

/**
 * A PdoRequester with transactions. The queries are only executed at this object destruction or when
 * commit() is explicitly called.
 *
 * If $autoTransaction is set to false, it will not perform the queries at destruction but only with commit().
 *
 * The DBMS MUST be compatible with transactions.
 *
 * @package Thor/Database/PdoExtension
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
final class Transaction extends Requester
{

    /**
     * @param Handler $handler
     * @param bool       $autoTransaction
     */
    public function __construct(Handler $handler, private bool $autoTransaction = true)
    {
        parent::__construct($handler);
        if ($this->autoTransaction && !$this->handler->getPdo()->inTransaction()) {
            $this->begin();
        }
    }

    /**
     *
     */
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

    /**
     * Sends a beginTransaction() to PDO.
     */
    public function begin(): void
    {
        $this->handler->getPdo()->beginTransaction();
    }

    /**
     * Sends a commit() to PDO.
     */
    public function commit(): void
    {
        $this->handler->getPdo()->commit();
    }

    /**
     * Sends a rollBack() to PDO.
     */
    public function rollback(): void
    {
        $this->handler->getPdo()->rollBack();
    }

}
