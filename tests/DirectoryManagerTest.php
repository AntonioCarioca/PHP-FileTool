<?php

namespace PHPFileTool\FileTool\Tests;

use PHPUnit\Framework\TestCase;
use PHPFileTool\FileTool\FileManager;
use PHPFileTool\FileTool\FileTool;
use PHPFileTool\FileTool\DirectoryManager;

class DirectoryManagerTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = TEST_DIR . '/test-dir';
    }

    protected function tearDown(): void
    {
        if (is_dir($this->dir)) {
            rmdir($this->dir);
        }
    }

    public function testCreateDirectory()
    {
        DirectoryManager::createDirectory($this->dir);

        $this->assertDirectoryExists($this->dir);
    }

    public function testDeleteDirectory()
    {
        mkdir($this->dir);

        DirectoryManager::deleteDirectory($this->dir);

        $this->assertDirectoryDoesNotExist($this->dir);
    }
}
