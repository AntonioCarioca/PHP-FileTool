# PHP FileTool

![PHP Version](https://img.shields.io/badge/PHP-8.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Packagist Version](https://img.shields.io/packagist/v/phpfiletool/filetool?style=for-the-badge&logo=packagist&logoColor=white&label=Packagist)
![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/AntonioCarioca/PHP-FileTool/ci.yml?style=for-the-badge&label=CI)
![GitHub Release](https://img.shields.io/github/v/release/AntonioCarioca/PHP-FileTool?style=for-the-badge&label=Release)
![License](https://img.shields.io/github/license/AntonioCarioca/PHP-FileTool?style=for-the-badge)

**PHP FileTool** is a simple PHP library for working with files and directories.

It provides useful methods to create, read, write, copy, rename, list and delete files or directories using a clean static API.

---

## Features

- Create files and directories
- Create multiple files at once
- Read and write file contents
- Copy files and file contents
- Copy all files from one directory to another
- Rename files and paths
- List directory files
- Delete files
- Delete empty directories
- Delete directories recursively
- Custom exceptions for safer error handling
- PSR-4 autoloading
- PHPUnit tests
- PHPStan static analysis
- PHPCS with PSR-12 standard
- GitHub Actions CI workflow

---

## Requirements

- PHP 8.4 or higher
- Composer

---

## Installation

Install the package with Composer:

```bash
composer require phpfiletool/filetool
```

---

## Basic Usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use PHPFileTool\FileTool\FileTool;

// Create a directory
FileTool::createDirectory('storage');

// Create a file
FileTool::createFile('storage', 'example.txt');

// Write content to the file
FileTool::writeFile('storage/example.txt', 'Hello, PHP FileTool!', 1);

// Read file content
$content = FileTool::readFile('storage/example.txt');

echo $content;
```

---

## File Operations

### Create a file

```php
use PHPFileTool\FileTool\FileTool;

$file = FileTool::createFile('storage', 'notes.txt');

echo $file;
```

You can also pass the file extension separately:

```php
FileTool::createFile('storage', 'notes', 'txt');
```

---

### Create multiple files

```php
FileTool::createMultipleFiles('storage', 'document', 3, 'txt');
```

This creates files like:

```text
storage/document1.txt
storage/document2.txt
storage/document3.txt
```

---

### Write to a file

```php
FileTool::writeFile('storage/notes.txt', 'New content', 1);
```

The third argument controls overwrite mode:

```php
0 // Append content
1 // Overwrite content
```

---

### Read a file

```php
$content = FileTool::readFile('storage/notes.txt');
```

---

### Copy a file

```php
FileTool::copyFile('storage/notes.txt', 'backup');
```

If the destination file already exists, PHP FileTool creates a unique name automatically:

```text
notes(1).txt
notes(2).txt
```

---

### Copy file content

```php
FileTool::copyFileContent(
    'storage/source.txt',
    'storage/destination.txt'
);
```

---

### Rename a file or path

```php
FileTool::renamePath('storage/old-name.txt', 'new-name', 'txt');
```

---

### Delete a file

```php
FileTool::deleteFile('storage/notes.txt');
```

---

## Directory Operations

### Create a directory

```php
FileTool::createDirectory('storage/uploads');
```

You can also define permissions:

```php
FileTool::createDirectory('storage/uploads', 0755);
```

---

### List files in a directory

```php
$files = FileTool::listFiles('storage');

print_r($files);
```

---

### Copy all files from one directory to another

```php
FileTool::copyAllFiles('storage', 'backup');
```

---

### Delete all files from a directory

```php
FileTool::deleteAllFiles('storage/cache');
```

This removes files inside the directory, but keeps the directory itself.

---

### Delete an empty directory

```php
FileTool::deleteDirectory('storage/empty-folder');
```

---

### Delete a directory recursively

```php
FileTool::deleteDirectoryRecursive('storage/temp');
```

> Be careful: this removes the directory and its contents recursively.

---

## Exception Handling

PHP FileTool uses custom exceptions so you can handle errors more safely.

```php
use PHPFileTool\FileTool\FileTool;
use PHPFileTool\FileTool\Exceptions\FileNotFoundException;
use PHPFileTool\FileTool\Exceptions\PermissionDeniedException;
use PHPFileTool\FileTool\Exceptions\FileToolException;

try {
    $content = FileTool::readFile('storage/missing.txt');
} catch (FileNotFoundException $exception) {
    echo 'The file was not found.';
} catch (PermissionDeniedException $exception) {
    echo 'Permission denied.';
} catch (FileToolException $exception) {
    echo 'FileTool error: ' . $exception->getMessage();
}
```

Available exceptions:

```text
FileToolException
FileNotFoundException
DirectoryNotFoundException
FileAlreadyExistsException
DirectoryAlreadyExistsException
PermissionDeniedException
InvalidPathException
InvalidOptionException
InvalidFileException
OperationFailedException
```

---

## Facade Methods

The main facade is:

```php
PHPFileTool\FileTool\FileTool
```

Main methods:

```php
FileTool::createDirectory(string $dir, int $permission = 0777): string;
FileTool::deleteDirectory(string $dir): bool;
FileTool::deleteDirectoryRecursive(string $dir): bool;
FileTool::deleteAllFiles(string $dir): bool;
FileTool::listFiles(string $dir): array;

FileTool::createFile(string $dir, string $file, ?string $type = null): string;
FileTool::createMultipleFiles(string $dir, string $file, int $quantity, ?string $type = null): array;
FileTool::copyFile(string $origin, string $destiny): string;
FileTool::copyAllFiles(string $origin, string $destiny): array;
FileTool::copyFileContent(string $fileOrigin, string $fileDestiny): string;
FileTool::readFile(string $dir): string;
FileTool::writeFile(string $dir, string $content, int $overwrite = 0): int;
FileTool::deleteFile(string $path): bool;
FileTool::renamePath(string $oldPath, string $newPath, ?string $type = null): string;
FileTool::renameAllFiles(string $dir, string $fileName, ?string $type = null): array;
```

---

## Legacy Method Aliases

For compatibility, some old method names are still available:

```php
FileTool::createDir();
FileTool::createMany();
FileTool::copy();
FileTool::copyAll();
FileTool::copyContent();
FileTool::purge();
FileTool::read();
FileTool::write();
FileTool::removeDir();
FileTool::removeFile();
FileTool::remove();
FileTool::removeAll();
FileTool::rename();
FileTool::renameFile();
FileTool::renameAll();
FileTool::show();
```

Prefer the new descriptive method names in new projects.

---

## Development

Install dependencies:

```bash
composer install
```

Run tests:

```bash
composer test
```

Run PHPStan:

```bash
composer analyse
```

Run PHPCS:

```bash
composer cs
```

Fix coding style automatically when possible:

```bash
composer cs:fix
```

---

## Quality Tools

This project uses:

- PHPUnit for unit tests
- PHPStan for static analysis
- PHPCS for PSR-12 code style
- GitHub Actions for continuous integration

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

---

## Contributing

Contributions are welcome.

Before opening a pull request, run:

```bash
composer test
composer analyse
composer cs
```

---

## Author

**Antonio Silva**

- GitHub: [AntonioCarioca](https://github.com/AntonioCarioca)
- Blog: [antoniosilva.hashnode.dev](https://antoniosilva.hashnode.dev/)

---

## License

This project is licensed under the GPL-3.0-or-later license.

See the [LICENSE](LICENSE) file for more details.
