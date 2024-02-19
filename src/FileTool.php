<?php

namespace PHPFileTool\FileTool;

/**
 * FileTool - 
 * 
 * @category File_Utilities
 * @package  PHPFileTool\FileTool
 * @author   XxZeroxX <antoniomarcos.silva@protonmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 *          v3.0 or later
 * @link     https://antoniosilva.hashnode.dev
 * @since    Release: 1.0
 */
class FileTool
{
    /**
     * Method for creating directories.
     * 
     * Creates a directory with the specified path, if it doesn't already exist.
     * 
     * @param string      $dir       The path of the directory to be created.
     * @param int|integer $permisson Directory permissions to be applied. 
     *                               The default is 0777.
     *                                
     * @return void                   There is no explicit feedback.
     */
    public static function createDir(string $dir, int $permisson = 0777): void
    {
        // Sanitize the directory path
        $dir = FileTool::sanitizeDirectory($dir);

        // Check if the directory already exists
        if (FileTool::checkDir($dir)) {
            // If the directory exists, handle the error and return
            ErrorHandler::handleError('The directory already exists', 500);
            return;
        }

        // Create the directory with the specified permissions
        mkdir($dir, $permisson, true);
    }

    /**
     * Method for creating a file.
     * 
     * Creates a file with the specified name in the given directory,
     * if it doesn't already exist.
     * 
     * @param string      $dir  The path of the directory where the file will
     *                          be created.
     * @param string      $file The name of the file to be created.
     * @param string|null $type Optional type of file to create. Default is null.
     * 
     * @return void              There is no explicit feedback.
     */
    public static function createFile(string $dir, string $file, string $type = null): void
    {
        // Sanitize the directory and file names
        $dir = FileTool::sanitizeDirectory($dir);
        $file = FileTool::sanitizeFile($file, $type);

        // Create the directory if it doesn't exist
        if (!is_dir($dir)) {
            FileTool::createDir($dir, 0777, true);
        }

        // Check if the directory is readable
        if (!is_readable($dir)) {
            ErrorHandler::handleError('Cannot access the directory', 500);
            return;
        }

        // Check if the directory is writable
        if (!is_writable($dir)) {
            ErrorHandler::handleError('Cannot write to directory', 500);
            return;
        }

        // Construct the full path of the file
        $fileFull = $dir . '/' . $file;

        // Check if the file already exists
        if (file_exists($fileFull)) {
            ErrorHandler::handleError('The file already exists.', 500);
            return;
        }

        // Attempt to create the file
        if (!fopen($fileFull, 'w')) {
            ErrorHandler::handleError('The file could not be created', 500);
            return;
        }

        // Clear stat cache
        clearstatcache();
    }

    /**
     * Method for sanitizing directory names.
     * 
     * Removes special characters and normalizes the format of a directory path.
     * 
     * @param string $dir The path of the directory to be sanitized.
     * 
     * @return string      The path of the sanitized directory.
     */
    private static function sanitizeDirectory(string $dir): string
    {
        // Remove special characters from the directory path
        $dirSanitized = preg_replace('/[^a-zA-Z0-9\/]+/', '', $dir);
        // Normalize directory separators (e.g., // to /)
        $dirSanitized = preg_replace('/\/+/', '/', $dirSanitized);
        // Trim any trailing slashes from the directory path
        $dirSanitized = trim($dirSanitized, '/');;
        // Return the sanitized directory path
        return $dirSanitized;
    }

    /**
     * Method for sanitizing a file name.
     * 
     * Removes special characters from the filename and optionally
     * transforms it according to a specified type.
     * 
     * @param string      $file The name of the file to be sanitized.
     * @param string|null $type Optional type for transforming the file name.
     * 
     * @return string|null       The name of the sanitized file, or null in case of
     *                           error.
     */
    private static function sanitizeFile(string $file, string $type = null): string|null
    {
        // Remove any characters that are not letters, numbers, spaces, or periods
        $file = preg_replace('/[^\p{L}\p{N}\s.]/u', '', $file);

        // Apply additional sanitization based on the specified type
        switch (strtolower($type)) {
        case 'camel':
            return FileTool::toCamel($file);
            break;
        case 'date':
            return FileTool::toDate($file);
            break;
        case 'lower':
            return FileTool::toLower($file);
            break;
        case 'pascal':
            return FileTool::toPascal($file);
            break;
        case 'upper':
            return FileTool::toUpper($file);
            break;
        case '':
            return FileTool::toNone($file);
            break;
        default:
            // If an invalid type is provided, handle the error and return null
            ErrorHandler::handleError('The following pattern does not exist.', 500);
            return null;
            break;
        }
    }
}
