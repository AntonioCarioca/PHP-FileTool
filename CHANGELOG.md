# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added

- Documentation improvements.
- More usage examples in the README.

---

## [2.0.0] - 2026-04-25

### Added

- Initial stable release.
- File management features:
  - Create files.
  - Create multiple files.
  - Read files.
  - Write files.
  - Copy files.
  - Copy file contents.
  - Rename files and paths.
  - Delete files.
- Directory management features:
  - Create directories.
  - Delete empty directories.
  - Delete directories recursively.
  - Delete all files inside a directory.
  - List files from a directory.
  - Copy all files from one directory to another.
- `FileTool` facade for simplified usage.
- `FileManager` class for file operations.
- `DirectoryManager` class for directory operations.
- `PathHelper` class for path and file name handling.
- Custom exception classes:
  - `FileToolException`
  - `FileNotFoundException`
  - `DirectoryNotFoundException`
  - `FileAlreadyExistsException`
  - `DirectoryAlreadyExistsException`
  - `PermissionDeniedException`
  - `InvalidPathException`
  - `InvalidOptionException`
  - `InvalidFileException`
  - `OperationFailedException`
- PHPUnit tests.
- Tests for expected exceptions.
- PHPStan static analysis.
- PHPCS with PSR-12 standard.
- GitHub Actions CI workflow.
- Composer scripts:
  - `composer test`
  - `composer analyse`
  - `composer cs`
  - `composer cs:fix`

### Changed

- Improved method names for clearer usage:
  - `createDir()` → `createDirectory()`
  - `removeDir()` → `deleteDirectory()`
  - `remove()` → `deleteFile()`
  - `removeAll()` → `deleteAllFiles()`
  - `purge()` → `deleteDirectoryRecursive()`
  - `read()` → `readFile()`
  - `write()` → `writeFile()`
  - `copy()` → `copyFile()`
  - `copyAll()` → `copyAllFiles()`
  - `rename()` → `renamePath()`
  - `renameAll()` → `renameAllFiles()`
  - `show()` → `listFiles()`
- Kept old method names as aliases for compatibility.
- Improved error handling with specific exceptions.
- Improved file copy behavior to avoid overwriting existing files.

### Fixed

- Fixed invalid parameter order for PHP 8+ compatibility.
- Fixed permission checks for read and copy operations.
- Fixed duplicate file name handling when copying files.
- Fixed unsafe path handling for directory normalization.
