<?php

namespace PHPFileTool\FileTool;

/**
 * FileTool - Library containing the main functions of the PHP file system.
 *
 * This class provides static methods for manipulating files and directories,
 * how to create directories, create files, sanitize directory and file names,
 * among other features related to the file system.
 * 
 * @category File_Utilities
 * @package  PHPFileTool\FileTool
 * @author   XxZeroxX <antoniomarcos.silva@protonmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 *           v3.0 or later
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
        if (is_dir($dir)) {
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

    /**
     * Converts a file name to CamelCase format.
     * 
     * Converts the file name to CamelCase, removing spaces and
     * capitalizing the first letter of each word.
     * 
     * @param string $file The name of the file to be converted.
     * 
     * @return string       The name of the file converted to CamelCase.
     */
    private static function toCamel(string $file): string
    {
        // Split the file name into words based on spaces
        $words = explode(' ', $file);
        // Remove any empty elements from the array
        $words = array_filter($words, fn($word) => $word !== '');
        // Reset array indices to start from 0
        $words = array_values($words);
        // Capitalize the first letter of each word
        $words = array_map('ucfirst', $words);
        // Convert the first word to lowercase
        $words[0] = strtolower($words[0]);
        // Concatenate the words to form the CamelCase string
        return $words = implode('', $words);
    }

    /**
     * Adds a date to the file name.
     * 
     * Adds the current date to the file name in 'Y-m-d' format.
     * 
     * @param string $file The name of the original file.
     * 
     * @return string       The name of the file with the date added.
     */
    private static function toDate(string $file): string
    {
        // Convert the file name to lowercase
        $file = mb_convert_case($file, MB_CASE_LOWER, "UTF-8");
        // Remove any spaces from the file name
        $file = preg_replace('/\s+/', '', $file);
        // Extract the file extension
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        // Extract the file name without the extension
        $fileName = pathinfo($file, PATHINFO_FILENAME);
        // Concatenate the original file name with the current date and extension
        return $file = $fileName . '_' . date('Y-m-d') . '.' . $fileExtension;
    }

    /**
     * Converts a file name to lowercase.
     * 
     * converts all letters in the filename to lower case and
     * removes blank spaces.
     * 
     * @param string $file The name of the file to be converted.
     * 
     * @return string       The name of the file converted to lower case.
     */
    private static function toLower(string $file): string
    {
        // Convert the file name to lowercase
        $file = mb_convert_case($file, MB_CASE_LOWER, "UTF-8");
        // Remove any spaces from the file name
        $file = preg_replace('/\s+/', '', $file);
        // Return the lowercase file name
        return $file;
    }

    /**
     * Keeps the file name unchanged.
     * 
     * Returns the filename without applying any pattern,
     * removing only special characters and spaces.
     * 
     * @param string $file The name of the file.
     * 
     * @return string       The name of the sanitized file.
     */
    private static function toNone(string $file): string
    {
        // Remove any spaces from the file name
        $file = preg_replace('/\s+/', '', $file);
        // Return the sanitized file name
        return $file;
    }
}
