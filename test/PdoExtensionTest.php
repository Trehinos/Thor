<?php

use Thor\Globals;
use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use PHPUnit\Framework\TestCase;
use Thor\Database\PdoExtension\PdoHandler;
use Thor\Database\PdoExtension\PdoRequester;

final class PdoExtensionTest extends TestCase
{
    private const DSN = '';
    private const USER = '';
    private const PASSWORD = '';

    public static function setUpBeforeClass(): void
    {
        Logger::setDefaultLogger(LogLevel::INFO, Globals::VAR_DIR . '/test-logs/');
    }

    public function testConnect(): PdoRequester
    {
        $pdoHandler = new PdoHandler(self::DSN, self::USER, self::PASSWORD);
        $requester = new PdoRequester($pdoHandler);
        $this->assertInstanceOf(PdoRequester::class, $requester);
        $this->assertInstanceOf(PdoHandler::class, $requester->getPdoHandler());
        $this->assertInstanceOf(PDO::class, $requester->getPdoHandler()->getPdo());

        return $requester;
    }

    /**
     * @depends      testConnect
     * @dataProvider dataProvider
     */
    public function testSelect(PdoRequester $requester): void
    {
        $row = $requester->request(
            '',
            []
        )->fetch();

        $this->assertNotEmpty($row);
        $this->assertSame('', $row['']);
        $this->assertSame('', $row['']);
    }

    public function dataProvider(): array
    {
        return [
            []
        ];
    }
}