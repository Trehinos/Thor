<?php

namespace DataTables\Database\Driver;
if (!defined('DATATABLES')) exit();

use PDO;
use DataTables\Database\Result;

class OdbcResult extends Result {
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Constructor
     */

    function __construct( $dbh, $stmt )
    {
        $this->_dbh = $dbh;
        $this->_stmt = $stmt;
    }

    private $_stmt;
    private $_dbh;

    public function count ()
    {
        return count($this->fetchAll());
    }


    public function fetch ( $fetchType=\PDO::FETCH_ASSOC )
    {
        return $this->_stmt->fetch( $fetchType );
    }


    public function fetchAll ( $fetchType=\PDO::FETCH_ASSOC )
    {
        return $this->_stmt->fetchAll( $fetchType );
    }


    public function insertId ()
    {
        return $this->_dbh->lastInsertId();
    }
}

