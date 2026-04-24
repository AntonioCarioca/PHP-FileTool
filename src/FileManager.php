<?php

namespace PHPFileTool\FileTool;

class DirectoryManager
{
    public static function createDirectory(string $dir, int $permission = 0777): string
    {
        $dir = PathHelper::normalizeDirectoryPath($dir);

        if (is_dir($dir)) {
            throw new \RuntimeException('The directory already exists');
        }

        if (!mkdir($dir, $permission, true)) {
            throw new \RuntimeException('The directory could not be created.');
        }

        return $dir;
    }

    public static function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            throw new \RuntimeException('The directory doesn\'t exist.');
        }

        if (!is_readable(dirname($dir))) {
            throw new \RuntimeException('Cannot access the directory.');
        }

        if (!is_writable(dirname($dir))) {
            throw new \RuntimeException('Cannot write to directory.');
        }

        if (count(scandir($dir)) !== 2) {
            throw new \RuntimeException('The directory is not empty.');
        }

        rmdir($dir);

        return true;
    }

    public static function deleteDirectoryRecursive(string $dir): bool
    {
        $dir = PathHelper::normalizeDirectoryPath($dir);

        if (!is_dir($dir)) {
            throw new \RuntimeException('The directory doesn\'t exist.');
        }

        if (!is_writable($dir)) {
            throw new \RuntimeException('The directory doesn\'t have write permission.');
        }

        $items = array_diff(scandir($dir), ['.', '..']);

        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                self::deleteDirectoryRecursive($path);
                continue;
            }

            if (!is_writable($path)) {
                throw new \RuntimeException('There are files that cannot be deleted.');
            }

            unlink($path);
        }

        rmdir($dir);

        return true;
    }

    public static function deleteAllFiles(string $dir): bool
    {
        if (!is_dir($dir)) {
            throw new \RuntimeException('The directory doesn\'t exist.');
        }

        if (!is_readable(dirname($dir))) {
            throw new \RuntimeException('Cannot access the directory.');
        }

        if (!is_writable(dirname($dir))) {
            throw new \RuntimeException('Cannot write to directory.');
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
            throw new \RuntimeException("$countFiles files could not be deleted.");
        }

        self::deleteDirectory($dir);

        return true;
    }

    public static function listFiles(string $dir): array
    {
        $dir = PathHelper::normalizeDirectoryPath($dir);

        if (!is_dir($dir)) {
            throw new \RuntimeException('The directory doesn\'t exist.');
        }

        if (!is_readable($dir)) {
            throw new \RuntimeException('Cannot access the directory');
        }

        return array_values(array_diff(scandir($dir), ['..', '.']));
    }
}
