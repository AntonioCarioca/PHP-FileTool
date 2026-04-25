<?php

namespace PHPFileTool\FileTool\Tests;

use PHPUnit\Framework\TestCase;
use PHPFileTool\FileTool\FileManager;
use PHPFileTool\FileTool\FileTool;

class FileManagerTest extends TestCase
{
    private string $file;

    protected function setUp(): void
    {
        $this->file = TEST_DIR . '/test.txt';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public function testCreateFile()
    {
        FileManager::createFile(TEST_DIR, 'test.txt');
        $this->assertFileExists($this->file);
    }

    public function testWriteAndReadFile(): void
    {
        FileManager::createFile(TEST_DIR, 'test.txt');

        FileManager::writeFile($this->file, 'Hello');

        $content = FileManager::readFile($this->file);

        $this->assertEquals('Hello', $content);
    }

    public function testDeleteFile()
    {
        file_put_contents($this->file, 'test');

        FileManager::deleteFile($this->file);

        $this->assertFileDoesNotExist($this->file);
    }

    public function testCopyFile(): void
    {
        file_put_contents($this->file, 'copy test');

        FileManager::copyFile($this->file, TEST_DIR);

        $copy = TEST_DIR . '/test(1).txt';

        $this->assertFileExists($copy);

        unlink($copy);
    }
}
