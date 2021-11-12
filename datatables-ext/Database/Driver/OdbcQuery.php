<?php

namespace DataTables\Database\Driver;
if (!defined('DATATABLES')) exit();

use PDO;
use DataTables\Database\Query;

class OdbcQuery extends Query
{
    protected $_identifier_limiter = array( '', '' );
    protected $_field_quote = '"';

    private $_stmt;

    static function connect( $user, $pass='', $host='', $port='', $db='', $dsn='' )
    {
        if ( is_array( $user ) ) {
            $opts = $user;
            $user = $opts['user'];
            $pass = $opts['pass'];
            $port = $opts['port'];
            $host = $opts['host'];
            $db   = $opts['db'];
            $dsn  = isset( $opts['dsn'] ) ? $opts['dsn'] : '';
            $pdoAttr = isset( $opts['pdoAttr'] ) ? $opts['pdoAttr'] : array();
        }

        if ( $port !== "" ) {
            $port = "port={$port};";
        }

        try {
            $pdoAttr[ PDO::ATTR_ERRMODE ] = PDO::ERRMODE_EXCEPTION;

            $pdo = @new PDO(
                "odbc:host={$host};{$port}dbname={$db}".self::dsnPostfix( $dsn ),
                $user,
                $pass,
                $pdoAttr
            );
        } catch (\PDOException $e) {
            // If we can't establish a DB connection then we return a DataTables
            // error.
            echo json_encode( array(
                                  "error" => "An error occurred while connecting to the database ".
                                             "'{$db}'. The error reported by the server was: ".$e->getMessage()
                              ) );
            exit(0);
        }

        return $pdo;
    }

    protected function _prepare( $sql )
    {
        $this->database()->debugInfo( $sql, $this->_bindings );

        $resource = $this->database()->resource();
        $this->_stmt = $resource->prepare( $sql );

        // bind values
        for ( $i=0 ; $i<count($this->_bindings) ; $i++ ) {
            $binding = $this->_bindings[$i];

            $this->_stmt->bindValue(
                $binding['name'],
                $binding['value'],
                $binding['type'] ?: PDO::PARAM_STR
            );
        }
    }

    protected function _exec()
    {
        try {
            $this->_stmt->execute();
        }
        catch (\PDOException $e) {
            error_log( "An SQL error occurred: ".$e->getMessage() );
            throw new \Exception( "An SQL error occurred: ".$e->getMessage() );
        }

        $resource = $this->database()->resource();
        return new OdbcResult( $resource, $this->_stmt );
    }
}

