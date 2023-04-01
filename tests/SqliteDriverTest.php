<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Tables\BaseDriverTest;
use Thor\Database\PdoTable\Driver\Sqlite;

final class SqliteDriverTest extends TestCase
{
    public function testDriverConstruction(): void
    {
        $driver = new Sqlite();
        $sql = $driver->createTable(BaseDriverTest::class);
        $this->assertTrue($sql === <<<§
            CREATE TABLE base_driver_test (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                description VARCHAR(255) NOT NULL DEFAULT
            )
            §);
    }

}
