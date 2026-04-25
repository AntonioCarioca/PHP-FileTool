<?php

namespace PHPFileTool\FileTool;

use PHPFileTool\FileTool\Exceptions\InvalidOptionException;
use PHPFileTool\FileTool\Exceptions\InvalidPathException;
use PHPFileTool\FileTool\Exceptions\OperationFailedException;

class PathHelper
{
    public static function normalizeDirectoryPath(string $dir): string
    {
        $dir = trim($dir);

        if ($dir === '') {
            throw new InvalidPathException('Directory path cannot be empty.');
        }

        $dir = str_replace('\\', '/', $dir);
        $dir = preg_replace('#/+#', '/', $dir);

        if ($dir === null) {
            throw new OperationFailedException('Failed to normalize directory path.');
        }

        $isAbsolute = str_starts_with($dir, '/');
        $parts = explode('/', trim($dir, '/'));
        $safeParts = [];

        foreach ($parts as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }

            if ($part === '..') {
                throw new InvalidPathException('Directory traversal is not allowed.');
            }

            $part = preg_replace('/[^\p{L}\p{N}._ -]/u', '', $part);
            $part = trim($part);

            if ($part !== '') {
                $safeParts[] = $part;
            }
        }

        $dir = implode('/', $safeParts);

        if ($dir === '') {
            throw new InvalidPathException('Directory path cannot be empty.');
        }

        return $isAbsolute ? '/' . $dir : $dir;
    }

    public static function sanitizeFileName(string $file, ?string $type = null): string
    {
        $file = preg_replace('/[^\p{L}\p{N}\s.]/u', '', $file);

        if ($file === null) {
            throw new OperationFailedException('Failed to sanitize file name.');
        }

        return match (strtolower($type ?? '')) {
            'camel' => self::toCamel($file),
            'date' => self::toDate($file),
            'lower' => self::toLower($file),
            'pascal' => self::toPascal($file),
            'upper' => self::toUpper($file),
            '' => self::toNone($file),
            default => throw new InvalidOptionException('Invalid file name format type.'),
        };
    }

    private static function toCamel(string $file): string
    {
        $words = explode(' ', $file);
        $words = array_filter($words, fn($word) => $word !== '');
        $words = array_values($words);

        if ($words === []) {
            return '';
        }

        $words = array_map('ucfirst', $words);
        $words[0] = strtolower($words[0]);

        return implode('', $words);
    }

    private static function toDate(string $file): string
    {
        $file = mb_convert_case($file, MB_CASE_LOWER, 'UTF-8');
        $file = preg_replace('/\s+/', '', $file);
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        $fileName = pathinfo($file, PATHINFO_FILENAME);

        return $fileExtension !== ''
            ? $fileName . '_' . date('Y-m-d') . '.' . $fileExtension
            : $fileName . '_' . date('Y-m-d');
    }

    private static function toLower(string $file): string
    {
        $file = mb_convert_case($file, MB_CASE_LOWER, 'UTF-8');

        return preg_replace('/\s+/', '', $file) ?? '';
    }

    private static function toNone(string $file): string
    {
        return preg_replace('/\s+/', '', $file) ?? '';
    }

    private static function toPascal(string $file): string
    {
        $file = mb_convert_case($file, MB_CASE_TITLE, 'UTF-8');

        return preg_replace('/\s+/', '', $file) ?? '';
    }

    private static function toUpper(string $file): string
    {
        $file = mb_convert_case($file, MB_CASE_UPPER, 'UTF-8');

        return preg_replace('/\s+/', '', $file) ?? '';
    }
}
