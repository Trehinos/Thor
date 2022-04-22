<?php

namespace Tests;

use Thor\Globals;
use Thor\FileSystem\FileSystem;
use Thor\FileSystem\Folder;
use PHPUnit\Framework\TestCase;

final class FileSystemTest extends TestCase
{

    public const PATH = Globals::VAR_DIR . 'fs-tests/';
    public const DATA = '12345678';

    public function testCreateFolder(): void
    {
        Folder::createIfNotExists(self::PATH);
        $this->assertTrue(FileSystem::exists(self::PATH));
        $this->assertTrue(FileSystem::hasPermission(self::PATH, FileSystem::ALL_ALL));

        Folder::createIfNotExists(self::PATH . 'folder1');
        $this->assertTrue(FileSystem::exists(self::PATH . 'folder1'));

        Folder::createIfNotExists(self::PATH . 'folder2');
        $this->assertTrue(FileSystem::exists(self::PATH . 'folder2'));

        Folder::createIfNotExists(self::PATH . 'folder3');
        $this->assertTrue(FileSystem::exists(self::PATH . 'folder3'));
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
        Folder::removeTree(self::PATH, removeFirst: false);
        $this->assertTrue(FileSystem::exists(self::PATH));
        $this->assertFalse(FileSystem::exists(self::PATH . 'folder1'));
        $this->assertFalse(FileSystem::exists(self::PATH . 'folder2'));
        $this->assertFalse(FileSystem::exists(self::PATH . 'folder3'));
        Folder::removeTree(self::PATH);
        $this->assertFalse(FileSystem::exists(self::PATH));
    }

}
