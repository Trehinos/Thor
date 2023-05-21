<?php

namespace Tests;

use PDO;
use Thor\Framework\Globals;
use Thor\Common\Debug\Logger;
use Thor\Common\Debug\LogLevel;
use PHPUnit\Framework\TestCase;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoTable\SchemaHelper;
use Thor\Database\PdoTable\Driver\Sqlite;
use Thor\Database\PdoExtension\PdoHandler;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoTable\PdoTable\PdoRowInterface;

final class PdoTableTest extends TestCase
{
    public const DSN = 'sqlite::memory:';
    public const USER = null;
    public const PASSWORD = null;

    public static PdoRequester $requester;
    public static SchemaHelper $schema;
    public static CrudHelper $crud;

    public static function setUpBeforeClass(): void
    {
        Logger::setDefaultLogger(LogLevel::INFO, Globals::VAR_DIR . '/test-logs/');
        $pdoHandler = new PdoHandler(self::DSN, self::USER, self::PASSWORD);
        self::$requester = new PdoRequester($pdoHandler);
        self::$schema = new SchemaHelper(self::$requester, new Sqlite(), TestTable::class);
        self::$crud = new CrudHelper(TestTable::class, self::$requester);
    }

    public function testConnect(): PdoRequester
    {
        $this->assertInstanceOf(PdoRequester::class, self::$requester);
        $this->assertInstanceOf(PdoHandler::class, self::$requester->getPdoHandler());
        $this->assertInstanceOf(PDO::class, self::$requester->getPdoHandler()->getPdo());

        return self::$requester;
    }

    /**
     * @depends      testConnect
     */
    public function testCreateTable(): void
    {
        $result = self::$schema->createTable();
        $this->assertTrue($result);
    }

    /**
     * @depends      testCreateTable
     * @dataProvider dataProvider
     */
    public function testInsert(int $id, string $data): void
    {
        $result = self::$crud->createOne($table = new TestTable($id, $data));

        $this->assertInstanceOf(PdoRowInterface::class, $table);
        $this->assertSame($id, (int)$result);
    }

    /**
     * @depends      testInsert
     */
    public function testSelectAll(): void
    {
        $rows = self::$crud->listAll();

        $this->assertNotEmpty($rows);
        $this->assertCount(2, $rows);
    }

    /**
     * @depends      testSelectAll
     * @dataProvider dataProvider
     */
    public function testSelectOne(int $id, string $data): void
    {
        $row = self::$crud->readOne([$id]);
        $this->assertNotEmpty($row);
        $this->assertSame($data, $row->data);
    }

    /**
     * @depends      testSelectOne
     * @dataProvider dataProvider
     */
    public function testDeleteOne(int $id): void
    {
        $row = self::$crud->readOne([$id]);
        $result = self::$crud->deleteOne($row);
        $this->assertTrue($result);

        $row = self::$crud->readOne([$id]);

        $this->assertNull($row);
    }

    /**
     * @depends      testDeleteOne
     */
    public function testDropTable(): void
    {
        $result = self::$schema->dropTable();
        $this->assertTrue($result);
    }

    public function dataProvider(): array
    {
        return [
            'Data set 1' => [1, "DATA TEST 1"],
            'Data set 2' => [2, "DATA TEST 2"],
        ];
    }
}
