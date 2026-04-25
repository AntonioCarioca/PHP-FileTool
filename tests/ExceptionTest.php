<?php

use PHPUnit\Framework\TestCase;
use PHPFileTool\FileTool\FileManager;
use PHPFileTool\FileTool\DirectoryManager;
use PHPFileTool\FileTool\Exceptions\FileNotFoundException;
use PHPFileTool\FileTool\Exceptions\DirectoryNotFoundException;
use PHPFileTool\FileTool\Exceptions\FileAlreadyExistsException;
use PHPFileTool\FileTool\Exceptions\DirectoryAlreadyExistsException;
use PHPFileTool\FileTool\Exceptions\InvalidPathException;

class ExceptionTest extends TestCase
{
    protected function tearDown(): void
    {
        $file = TEST_DIR . '/already-exists.txt';
        $dir = TEST_DIR . '/already-exists-dir';

        if (file_exists($file)) {
            unlink($file);
        }

        if (is_dir($dir)) {
            rmdir($dir);
        }
    }

    public function testReadFileThrowsExceptionWhenFileDoesNotExist(): void
    {
        $this->expectException(FileNotFoundException::class);

        FileManager::readFile(TEST_DIR . '/missing.txt');
    }

    public function testDeleteFileThrowsExceptionWhenFileDoesNotExist(): void
    {
        $this->expectException(FileNotFoundException::class);

        FileManager::deleteFile(TEST_DIR . '/missing.txt');
    }

    public function testCreateFileThrowsExceptionWhenFileAlreadyExists(): void
    {
        $file = TEST_DIR . '/already-exists.txt';

        file_put_contents($file, 'test');

        $this->expectException(FileAlreadyExistsException::class);

        FileManager::createFile(TEST_DIR, 'already-exists.txt');
    }

    public function testDeleteDirectoryThrowsExceptionWhenDirectoryDoesNotExist(): void
    {
        $this->expectException(DirectoryNotFoundException::class);

        DirectoryManager::deleteDirectory(TEST_DIR . '/missing-dir');
    }

    public function testCreateDirectoryThrowsExceptionWhenDirectoryAlreadyExists(): void
    {
        $dir = TEST_DIR . '/already-exists-dir';

        mkdir($dir);

        $this->expectException(DirectoryAlreadyExistsException::class);

        DirectoryManager::createDirectory($dir);
    }

    public function testInvalidPathThrowsException(): void
    {
        $this->expectException(InvalidPathException::class);

        FileManager::readFile('../secret.txt');
    }
}
