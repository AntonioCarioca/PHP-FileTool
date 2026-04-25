<?php

namespace PHPFileTool\FileTool;
use PHPFileTool\FileTool\Exceptions\DirectoryNotFoundException;
use PHPFileTool\FileTool\Exceptions\FileAlreadyExistsException;
use PHPFileTool\FileTool\Exceptions\FileNotFoundException;
use PHPFileTool\FileTool\Exceptions\InvalidFileException;
use PHPFileTool\FileTool\Exceptions\InvalidOptionException;
use PHPFileTool\FileTool\Exceptions\OperationFailedException;
use PHPFileTool\FileTool\Exceptions\PermissionDeniedException;


class FileManager
{
    public static function createFile(string $dir, string $file, ?string $type = null): string
    {
        $dir = PathHelper::normalizeDirectoryPath($dir);
        $file = PathHelper::sanitizeFileName($file, $type);

        if (!is_dir($dir)) {
            DirectoryManager::createDirectory($dir, 0777);
        }

        if (!is_readable($dir)) {
            throw new PermissionDeniedException('Directory is not readable.');
        }

        if (!is_writable($dir)) {
            throw new PermissionDeniedException('Directory is not writable.');
        }

        $fileFull = $dir . '/' . $file;

        if (file_exists($fileFull)) {
            throw new FileAlreadyExistsException('File already exists.');
        }

        $newFile = fopen($fileFull, 'w');

        if ($newFile === false) {
            throw new OperationFailedException('Failed to create file.');
        }

        fclose($newFile);
        clearstatcache();

        return $fileFull;
    }

    public static function createMultipleFiles(
        string $dir, 
        string $file,
        int $quantity,
        ?string $type = null
    ): array
    {
        $dir = PathHelper::normalizeDirectoryPath($dir);
        $file = PathHelper::sanitizeFileName($file, $type);

        if ($quantity <= 0) {
            throw new InvalidOptionException('Quantity must be greater than zero.');
        }

        if (!is_dir($dir)) {
            DirectoryManager::createDirectory($dir, 0777);
        }

        if (!is_readable($dir)) {
            throw new PermissionDeniedException('Directory is not readable.');
        }

        if (!is_writable($dir)) {
            throw new PermissionDeniedException('Directory is not writable.');
        }

        $fileFull = $dir . '/' . $file;
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        $fileName = pathinfo($file, PATHINFO_FILENAME);

        if (file_exists($fileFull)) {
            throw new FileAlreadyExistsException('File already exists.');
        }

        $createdFiles = [];

        for ($i = 1; $i <= $quantity; $i++) {
            $newFile = fopen($fileFull, 'w');

            if ($newFile === false) {
                throw new OperationFailedException('Failed to create file.');
            }

            fclose($newFile);
            $createdFiles[] = $fileFull;
            $suffix = '_' . $i;
            $fileFull = $fileExtension !== ''
                ? $dir . '/' . $fileName . $suffix . '.' . $fileExtension
                : $dir . '/' . $fileName . $suffix;
        }

        clearstatcache();

        return $createdFiles;
    }

    public static function copyFile(string $origin, string $destiny): string
    {
        if (!is_file($origin)) {
            throw new FileNotFoundException('Origin file was not found.');
        }

        if (!is_readable($origin)) {
            throw new PermissionDeniedException('Origin file is not readable.');
        }

        $destiny = PathHelper::normalizeDirectoryPath($destiny);

        if (!is_dir($destiny)) {
            DirectoryManager::createDirectory($destiny, 0777);
        }

        if (!is_writable($destiny)) {
            throw new PermissionDeniedException('Destination directory is not writable.');
        }

        $fileName = pathinfo(basename($origin), PATHINFO_FILENAME);
        $fileExtension = pathinfo(basename($origin), PATHINFO_EXTENSION);
        $destinationFile = $destiny . '/' . basename($origin);
        $i = 1;

        while (file_exists($destinationFile)) {
            $suffix = '(' . $i . ')';
            $destinationFile = $fileExtension !== ''
                ? $destiny . '/' . $fileName . $suffix . '.' . $fileExtension
                : $destiny . '/' . $fileName . $suffix;
            $i++;
        }

        if (!copy($origin, $destinationFile)) {
            throw new OperationFailedException('Failed to copy file.');
        }

        return $destinationFile;
    }

    public static function copyAllFiles(string $origin, string $destiny): array
    {
        if (!is_dir($origin)) {
            throw new DirectoryNotFoundException('Directory was not found.');
        }

        if (!is_readable($origin)) {
            throw new PermissionDeniedException('Origin directory is not readable.');
        }

        $destiny = PathHelper::normalizeDirectoryPath($destiny);

        if (!is_dir($destiny)) {
            DirectoryManager::createDirectory($destiny, 0777);
        }

        if (!is_writable($destiny)) {
            throw new PermissionDeniedException('Destination directory is not writable.');
        }

        $files = scandir($origin);
        $countFiles = 0;
        $copiedFiles = [];

        foreach ($files as $file) {
            if (!is_file($origin . '/' . $file)) {
                continue;
            }

            if (is_readable($origin . '/' . $file) && !file_exists($destiny . '/' . $file)) {
                if (!copy($origin . '/' . $file, $destiny . '/' . $file)) {
                    $countFiles++;
                    continue;
                }

                $copiedFiles[] = $destiny . '/' . $file;
                continue;
            }

            $countFiles++;
        }

        if ($countFiles > 0) {
            throw new OperationFailedException("{$countFiles} file(s) could not be copied.");
        }

        return $copiedFiles;
    }

    public static function copyFileContent(string $fileOrigin, string $fileDestiny): string
    {
        $fileOrigin = PathHelper::normalizeDirectoryPath(dirname($fileOrigin)) . '/' .
                      PathHelper::sanitizeFileName(basename($fileOrigin));
        $fileDestiny = PathHelper::normalizeDirectoryPath(dirname($fileDestiny)) . '/' .
                       PathHelper::sanitizeFileName(basename($fileDestiny));

        if (!file_exists($fileOrigin) || !is_dir(dirname($fileOrigin))) {
            throw new FileNotFoundException('Source file or directory was not found.');
        }

        if (!is_readable($fileOrigin) || !is_readable(dirname($fileOrigin))) {
            throw new PermissionDeniedException('Source file or directory is not readable.');
        }

        if (!is_dir(dirname($fileDestiny))) {
            DirectoryManager::createDirectory(dirname($fileDestiny));
        }

        if (!file_exists($fileDestiny)) {
            self::createFile(dirname($fileDestiny), basename($fileDestiny));
        }

        if (!is_writable($fileDestiny) || !is_writable(dirname($fileDestiny))) {
            throw new PermissionDeniedException('Target file or directory is not writable.');
        }

        $content = file_get_contents($fileOrigin);

        if ($content === false) {
            throw new OperationFailedException('Failed to read source file content.');
        }

        $file = fopen($fileDestiny, 'w');

        if ($file === false) {
            throw new OperationFailedException('Failed to open destination file for writing.');
        }

        fwrite($file, $content);
        fclose($file);

        return $fileDestiny;
    }

    public static function readFile(string $dir): string
    {
        $dir = PathHelper::normalizeDirectoryPath(dirname($dir)) . '/' .
               PathHelper::sanitizeFileName(basename($dir));

        if (!is_dir(dirname($dir))) {
            throw new DirectoryNotFoundException('Directory was not found.');
        }

        if (!is_readable(dirname($dir))) {
            throw new PermissionDeniedException('Directory is not readable.');
        }

        if (!file_exists($dir)) {
            throw new FileNotFoundException('File was not found.');
        }

        if (!is_readable($dir)) {
            throw new PermissionDeniedException('File is not readable.');
        }

        $content = file_get_contents($dir);

        if ($content === false) {
            throw new OperationFailedException('Failed to read file.');
        }

        return $content;
    }

    public static function deleteFile(string $path): bool
    {
        if (!is_dir(dirname($path))) {
            throw new DirectoryNotFoundException('Directory was not found.');
        }

        if (!is_readable(dirname($path))) {
            throw new PermissionDeniedException('Directory is not readable.');
        }

        if (!is_writable(dirname($path))) {
            throw new PermissionDeniedException('Directory is not writable.');
        }

        if (!file_exists($path)) {
            throw new FileNotFoundException('File was not found.');
        }

        if (!is_file($path)) {
            throw new InvalidFileException('Path is not a file.');
        }

        if (!is_writable($path)) {
            throw new PermissionDeniedException('File is not writable and cannot be deleted.');
        }

        unlink($path);

        return true;
    }

    public static function renamePath(string $oldPath, string $newPath, ?string $type = null): string
    {
        if (!is_dir($oldPath) && !file_exists($oldPath)) {
            throw new FileNotFoundException('Source path was not found.');
        }

        if (!is_readable(dirname($oldPath)) || !is_readable($oldPath)) {
            throw new PermissionDeniedException('Source path or parent directory is not readable.');
        }

        if (!is_writable(dirname($oldPath)) || !is_writable($oldPath)) {
            throw new PermissionDeniedException('Source path or parent directory is not writable.');
        }

        if (!pathinfo($newPath, PATHINFO_EXTENSION)) {
            $newPath = PathHelper::normalizeDirectoryPath($newPath);
        } else {
            $file = pathinfo($newPath, PATHINFO_FILENAME) . '.' . pathinfo($newPath, PATHINFO_EXTENSION);
            $file = PathHelper::sanitizeFileName($file, $type);
            $newPath = PathHelper::normalizeDirectoryPath(dirname($newPath)) . '/' . $file;
        }

        if (!rename($oldPath, $newPath)) {
            throw new OperationFailedException('Failed to rename path.');
        }

        return $newPath;
    }

    public static function renameAllFiles(string $dir, string $fileName, ?string $type = null): array
    {
        if (!is_dir($dir)) {
            throw new DirectoryNotFoundException('Directory was not found.');
        }

        if (!is_readable($dir)) {
            throw new PermissionDeniedException('Directory is not readable.');
        }

        if (!is_writable($dir)) {
            throw new PermissionDeniedException('Directory is not writable.');
        }

        $fileName = PathHelper::sanitizeFileName($fileName, $type);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);
        $countFiles = 0;
        $version = 1;
        $renamedFiles = [];

        $dh = opendir($dir);

        if ($dh !== false) {
            while (($file = readdir($dh)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $newFile = $fileExtension !== ''
                    ? $fileName . '_' . $version . '.' . $fileExtension
                    : $fileName . '_' . $version;

                if (!is_readable($dir . '/' . $file) || !is_writable($dir . '/' . $file)) {
                    $countFiles++;
                } elseif (!rename($dir . '/' . $file, $dir . '/' . $newFile)) {
                    $countFiles++;
                } else {
                    $renamedFiles[] = $dir . '/' . $newFile;
                }

                $version++;
            }

            closedir($dh);
        }

        if ($countFiles > 0) {
            throw new OperationFailedException("{$countFiles} file(s) could not be renamed.");
        }

        return $renamedFiles;
    }

    public static function writeFile(string $dir, string $content, int $overwrite = 0): int
    {
        $dir = PathHelper::normalizeDirectoryPath(dirname($dir)) . '/' .
               PathHelper::sanitizeFileName(basename($dir));

        if (!is_dir(dirname($dir)) || !file_exists($dir)) {
            throw new FileNotFoundException('File or directory was not found.');
        }

        if (!is_writable(dirname($dir)) || !is_writable($dir)) {
            throw new PermissionDeniedException('File or directory is not writable.');
        }

        if ($overwrite === 0) {
            $file = fopen($dir, 'a');
        } elseif ($overwrite === 1) {
            $file = fopen($dir, 'w');
        } else {
            throw new InvalidOptionException('Overwrite must be 0 (append) or 1 (replace).');
        }

        if ($file === false) {
            throw new OperationFailedException('Failed to open file.');
        }

        $bytes = fwrite($file, $content);

        if ($bytes === false) {
            fclose($file);
            throw new OperationFailedException('Failed to write content to file.');
        }

        fclose($file);

        return $bytes;
    }
}
