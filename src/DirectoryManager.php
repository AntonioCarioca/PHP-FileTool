<?php

namespace PHPFileTool\FileTool;

use PHPFileTool\FileTool\Exceptions\DirectoryAlreadyExistsException;
use PHPFileTool\FileTool\Exceptions\DirectoryNotFoundException;
use PHPFileTool\FileTool\Exceptions\OperationFailedException;
use PHPFileTool\FileTool\Exceptions\PermissionDeniedException;

class DirectoryManager
{
    public static function createDirectory(string $dir, int $permission = 0777): string
    {
        $dir = PathHelper::normalizeDirectoryPath($dir);

        if (is_dir($dir)) {
            throw new DirectoryAlreadyExistsException('Directory already exists.');
        }

        if (!mkdir($dir, $permission, true)) {
            throw new OperationFailedException('Failed to create directory.');
        }

        return $dir;
    }

    public static function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            throw new DirectoryNotFoundException('Directory was not found.');
        }

        if (!is_readable(dirname($dir))) {
            throw new PermissionDeniedException('Directory is not readable.');
        }

        if (!is_writable(dirname($dir))) {
            throw new PermissionDeniedException('Directory is not writable.');
        }

        if (count(scandir($dir)) !== 2) {
            throw new OperationFailedException('Directory is not empty.');
        }

        rmdir($dir);

        return true;
    }

    public static function deleteDirectoryRecursive(string $dir): bool
    {
        $dir = PathHelper::normalizeDirectoryPath($dir);

        if (!is_dir($dir)) {
            throw new DirectoryNotFoundException('Directory was not found.');
        }

        if (!is_writable($dir)) {
            throw new PermissionDeniedException('Directory is not writable.');
        }

        $items = array_diff(scandir($dir), ['.', '..']);

        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                self::deleteDirectoryRecursive($path);
                continue;
            }

            if (!is_writable($path)) {
                throw new PermissionDeniedException(
                    'One or more files cannot be deleted because they are not writable.'
                );
            }

            unlink($path);
        }

        rmdir($dir);

        return true;
    }

    public static function deleteAllFiles(string $dir): bool
    {
        if (!is_dir($dir)) {
            throw new DirectoryNotFoundException('Directory was not found.');
        }

        if (!is_readable(dirname($dir))) {
            throw new PermissionDeniedException('Directory is not readable.');
        }

        if (!is_writable(dirname($dir))) {
            throw new PermissionDeniedException('Directory is not writable.');
        }

        $files = scandir($dir);
        $countFiles = 0;

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && is_file($dir . '/' . $file)) {
                $filePath = $dir . '/' . $file;

                if (!is_writable($filePath)) {
                    $countFiles++;
                    continue;
                }

                unlink($filePath);
            }
        }

        if ($countFiles > 0) {
            throw new OperationFailedException("{$countFiles} file(s) could not be deleted.");
        }

        self::deleteDirectory($dir);

        return true;
    }

    public static function listFiles(string $dir): array
    {
        $dir = PathHelper::normalizeDirectoryPath($dir);

        if (!is_dir($dir)) {
            throw new DirectoryNotFoundException('Directory was not found.');
        }

        if (!is_readable($dir)) {
            throw new PermissionDeniedException('Directory is not readable.');
        }

        return array_values(array_diff(scandir($dir), ['..', '.']));
    }
}
