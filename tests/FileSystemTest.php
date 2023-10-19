<?php

namespace Tests;

use Thor\FileSystem\Permissions;
use Thor\Globals;
use Thor\FileSystem\FileSystem;
use Thor\FileSystem\Folders;
use PHPUnit\Framework\TestCase;

final class FileSystemTest extends TestCase
{

    public const PATH = Globals::VAR_DIR . 'fs-tests/';
    public const DATA = '12345678';

    public function testCreateFolder(): void
    {
        Folders::createIfNotExists(self::PATH, Permissions::OWNER_ALL);
        $this->assertTrue(FileSystem::exists(self::PATH));
        $this->assertTrue(Permissions::has(self::PATH, Permissions::OWNER_ALL));
        $this->assertEquals('drwx------', Permissions::stringRepresentationFor(self::PATH));
    }

    /**
     * @depends testCreateFolder
     */
    public function testFile(): void
    {
        FileSystem::write(self::PATH . 'newfile', self::DATA);
        $this->assertTrue(FileSystem::exists(self::PATH . 'newfile'));
        $this->assertEquals(self::DATA, FileSystem::read(self::PATH . 'newfile'));
    }

    /**
     * @depends testFile
     */
    public function testRemoveFolder(): void
    {
        Folders::removeTree(self::PATH, removeRoot: false);
        $this->assertTrue(FileSystem::exists(self::PATH));
        $this->assertFalse(FileSystem::exists(self::PATH . 'folder'));
        Folders::removeTree(self::PATH);
        $this->assertFalse(FileSystem::exists(self::PATH));
    }

}
