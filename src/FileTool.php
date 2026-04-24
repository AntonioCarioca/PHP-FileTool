<?php

namespace PHPFileTool\FileTool;

/**
 * FileTool - Facade for file and directory operations.
 */
class FileTool
{
    public static function createDirectory(string $dir, int $permission = 0777): string
    {
        return DirectoryManager::createDirectory($dir, $permission);
    }

    public static function deleteDirectory(string $dir): bool
    {
        return DirectoryManager::deleteDirectory($dir);
    }

    public static function deleteDirectoryRecursive(string $dir): bool
    {
        return DirectoryManager::deleteDirectoryRecursive($dir);
    }

    public static function deleteAllFiles(string $dir): bool
    {
        return DirectoryManager::deleteAllFiles($dir);
    }

    public static function listFiles(string $dir): array
    {
        return DirectoryManager::listFiles($dir);
    }

    public static function createFile(string $dir, string $file, ?string $type = null): string
    {
        return FileManager::createFile($dir, $file, $type);
    }

    public static function createMultipleFiles(string $dir, string $file, ?string $type = null, int $quantity): array
    {
        return FileManager::createMultipleFiles($dir, $file, $type, $quantity);
    }

    public static function copyFile(string $origin, string $destiny): string
    {
        return FileManager::copyFile($origin, $destiny);
    }

    public static function copyAllFiles(string $origin, string $destiny): array
    {
        return FileManager::copyAllFiles($origin, $destiny);
    }

    public static function copyFileContent(string $fileOrigin, string $fileDestiny): string
    {
        return FileManager::copyFileContent($fileOrigin, $fileDestiny);
    }

    public static function readFile(string $dir): string
    {
        return FileManager::readFile($dir);
    }

    public static function deleteFile(string $path): bool
    {
        return FileManager::deleteFile($path);
    }

    public static function renamePath(string $oldPath, string $newPath, ?string $type = null): string
    {
        return FileManager::renamePath($oldPath, $newPath, $type);
    }

    public static function renameAllFiles(string $dir, string $fileName, ?string $type = null): array
    {
        return FileManager::renameAllFiles($dir, $fileName, $type);
    }

    public static function writeFile(string $dir, string $content, int $overwrite = 0): int
    {
        return FileManager::writeFile($dir, $content, $overwrite);
    }

    public static function write(string $dir, string $content, int $overwrite = 0): int
    {
        return self::writeFile($dir, $content, $overwrite);
    }

    public static function createDir(string $dir, int $permission = 0777): string
    {
        return self::createDirectory($dir, $permission);
    }

    public static function createMany(string $dir, string $file, ?string $type = null, int $quantity): array
    {
        return self::createMultipleFiles($dir, $file, $type, $quantity);
    }

    public static function copy(string $origin, string $destiny): string
    {
        return self::copyFile($origin, $destiny);
    }

    public static function copyAll(string $origin, string $destiny): array
    {
        return self::copyAllFiles($origin, $destiny);
    }

    public static function copyContent(string $fileOrigin, string $fileDestiny): string
    {
        return self::copyFileContent($fileOrigin, $fileDestiny);
    }

    public static function purge(string $dir): bool
    {
        return self::deleteDirectoryRecursive($dir);
    }

    public static function read(string $dir): string
    {
        return self::readFile($dir);
    }

    public static function removeDir(string $dir): bool
    {
        return self::deleteDirectory($dir);
    }

    public static function removeFile(string $path): bool
    {
        return self::deleteFile($path);
    }

    public static function remove(string $path): bool
    {
        return self::deleteFile($path);
    }

    public static function removeAll(string $dir): bool
    {
        return self::deleteAllFiles($dir);
    }

    public static function rename(string $oldPath, string $newPath, ?string $type = null): string
    {
        return self::renamePath($oldPath, $newPath, $type);
    }

    public static function renameFile(string $oldPath, string $newPath, ?string $type = null): string
    {
        return self::renamePath($oldPath, $newPath, $type);
    }

    public static function renameAll(string $dir, string $fileName, ?string $type = null): array
    {
        return self::renameAllFiles($dir, $fileName, $type);
    }

    public static function show(string $dir): array
    {
        return self::listFiles($dir);
    }
}
