<?php

namespace Tests;

use PDO;
use Thor\Globals;
use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use PHPUnit\Framework\TestCase;
use Thor\Database\PdoExtension\Handler;
use Thor\Database\PdoExtension\Requester;

final class PdoExtensionTest extends TestCase
{
    public const DSN = 'sqlite::memory:';
    public const USER = null;
    public const PASSWORD = null;

    public static Requester $requester;

    public static function setUpBeforeClass(): void
    {
        Logger::setDefaultLogger(LogLevel::INFO, Globals::VAR_DIR . '/test-logs/');
        $pdoHandler = new Handler(self::DSN, self::USER, self::PASSWORD);
        self::$requester = new Requester($pdoHandler);
    }

    public function testConnect(): Requester
    {
        $this->assertInstanceOf(Requester::class, self::$requester);
        $this->assertInstanceOf(Handler::class, self::$requester->getPdoHandler());
        $this->assertInstanceOf(PDO::class, self::$requester->getPdoHandler()->getPdo());

        return self::$requester;
    }

    /**
     * @depends      testConnect
     */
    public function testCreateTable(): void
    {
        $result = self::$requester->execute(
            'CREATE TABLE test (id INTEGER NOT NULL, data VARCHAR(255) NOT NULL DEFAULT \'\')'
        );

        $this->assertTrue($result);
    }

    /**
     * @depends testCreateTable
     * @dataProvider dataProvider
     */
    public function testInsert(int $id, string $data): void
    {
        $stmtResult = self::$requester->execute(
            'INSERT INTO test VALUES (?,?)',
            [$id, $data]
        );

        $this->assertTrue($stmtResult);
    }

    /**
     * @depends      testInsert
     */
    public function testSelectAll(): void
    {
        $rows = self::$requester->request('SELECT * FROM test')->fetchAll();

        $this->assertNotEmpty($rows);
        $this->assertCount(2, $rows);
    }

    /**
     * @depends      testSelectAll
     * @dataProvider dataProvider
     */
    public function testSelectOne(int $id, string $data): void
    {
        $row = self::$requester->request(
            'SELECT * FROM test WHERE id=?',
            [$id]
        )->fetch();

        $this->assertNotEmpty($row);
        $this->assertSame($data, $row['data']);
    }

    /**
     * @depends      testSelectOne
     */
    public function testTruncate(): void
    {
        $result = self::$requester->execute('DELETE FROM test');

        $this->assertTrue($result);

        $rows = self::$requester->request('SELECT * FROM test')->fetchAll();

        $this->assertEmpty($rows);
    }

    /**
     * @depends      testTruncate
     */
    public function testDropTable(): void
    {
        $result = self::$requester->execute('DROP TABLE test');

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
