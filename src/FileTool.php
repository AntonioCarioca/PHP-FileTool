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
     * Creates several files with sequential names.
     * 
     * Creates a specified number of files with sequential names in the given
     * directory provided, if they don't already exist.
     * 
     * @param  string      $dir      The path of the directory where the files will 
     *                               be created.
     * @param  string      $file     The base name of the files to be created.
     * @param  string|null $type     Optional type of files to be created. Default 
     *                               is null.
     * @param  int         $quantity The number of files to be created.
     * 
     * @return void                There is no explicit feedback.
     */
    public static function createMany(string $dir, string $file, string $type = null, int $quantity): void
    {
    	// Sanitize the directory and file names
    	$dir = FileTool::sanitizeDirectory($dir);
		$file = FileTool::sanitizeFile($file, $type);

		// Check if the quantity is valid
		if ($quantity <= 0) {
			ErrorHandler::handleError('The quantity cannot be 0 and/or negative.', 500);
			return;
		}

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
		// Extract the file extension and file name without extension
		$fileExtension = pathinfo($file, PATHINFO_EXTENSION);
		$fileName = pathinfo($file, PATHINFO_FILENAME);

		// Check if the base file already exists
		if (file_exists($fileFull)) {
			ErrorHandler::handleError('The file already exists.', 500);
			return;
		}

		// Create the specified number of files with sequential names
		for ($i = 1; $i <= $quantity; $i++) {
			// Attempt to create the file
			if (!fopen($fileFull, 'w')) {
				ErrorHandler::handleError('The file could not be created', 500);
				return;
			}
			// Generate the next file name for the next iteration
			$fileFull = $dir . '/' . $fileName . '_' . $i . '.' . $fileExtension;
		}

		// Clear stat cache
		clearstatcache();
    }

    /**
     * Removes an empty directory.
     * 
     * Removes the specified directory if it is empty.
     * 
     * @param  string $dir The path of the directory to be removed.
     * 
     * @return void      There is no explicit feedback.
     */
    public static function removeDir(string $dir): void
    {
    	// Check if the directory exists
    	if (!is_dir($dir)) {
			ErrorHandler::handleError('The directory doesn\'t exist.', 500);
			return;
		}

		// Check if the parent directory is readable
		if (!is_readable(dirname($dir))) {
			ErrorHandler::handleError('Cannot access the directory.', 500);
			return;
		}

		// Check if the parent directory is writable
		if (!is_writable(dirname($dir))) {
			ErrorHandler::handleError('Cannot write to directory.', 500);
			return;
		}

		// Check if the directory is empty
		if (count(scandir($dir)) !== 2) {
			ErrorHandler::handleError('The directory is not empty or an error occurred when deleting the directory.', 500);
			return;
		}

		// Remove the directory
		rmdir($dir);
    }

    /**
     * Remove a file.
     * 
     * Removes the specified file, if it exists.
     * 
     * @param  string $path The path of the file to be removed.
     * 
     * @return void       There is no explicit feedback.
     */
    public static function removeFile(string $path): void
    {
    	// Check if the directory of the file exists
    	if (!is_dir(dirname($path))) {
			ErrorHandler::handleError('The directory doesn\'t exist.', 500);
			return;
		}

		// Check if the parent directory is readable
		if (!is_readable(dirname($path))) {
			ErrorHandler::handleError('Cannot access the directory.', 500);
			return;
		}

		// Check if the parent directory is writable
		if (!is_writable(dirname($path))) {
			ErrorHandler::handleError('Cannot write to directory.', 500);
			return;
		}

		// Check if the file exists
		if (!file_exists($path)) {
			ErrorHandler::handleError('The file doesn\'t exist.', 500);
			return;
		}

		// Check if it's a regular file
		if (!is_file($path)) {
			ErrorHandler::handleError('It\'s not a file.', 500);
			return;
		}

		// Check if the file is writable
		if (!is_writable($path)) {
			ErrorHandler::handleError('The file cannot be deleted because it is not writable.', 500);
			return;
		}

		// Remove the file
		unlink($path);
    }

    /**
     * Removes all files and the specified directory.
     * 
     * Removes all files within the specified directory, if they exist,
     * and then removes the directory itself.
     * 
     * @param  string $dir The path of the directory to be removed.
     * 
     * @return void      There is no explicit feedback.
     */
    public static function removeAll(string $dir): void
    {
        // Check if the directory exists
        if (!is_dir($dir)) {
            ErrorHandler::handleError('The directory doesn\'t exist.', 500);
            return;
        }

        // Check if the parent directory is readable
        if (!is_readable(dirname($dir))) {
            ErrorHandler::handleError('Cannot access the directory.', 500);
            return;
        }

        // Check if the parent directory is writable
        if (!is_writable(dirname($dir))) {
            ErrorHandler::handleError('Cannot write to directory.', 500);
            return;
        }

        // Get the list of files in the directory
        $files = scandir($dir);
        $countFiles = 0;

        // Iterate through the files in the directory
        foreach ($files as $file) {
            // Check if the file is not the current directory (.) or parent directory
            // (..) and is a regular file
            if ($file != '.' && $file != '..' && is_file($dir . '/' . $file)) {
                $filePath = $dir . '/' . $file;
                // Check if the file is writable
                if (!is_writable($filePath)) {
                    $countFiles++;
                } else {
                    // Remove the file
                    unlink($filePath);
                }
            }
        }

        // If there are files that couldn't be deleted, handle the error
        if ($countFiles > 0) {
            ErrorHandler::handleError("$countFiles files could not be deleted.", 500);
            return;
        }

        // Remove the directory
        FileTool::removeDir($dir);
    }

    /**
     * Renames a directory or file.
     * 
     * Renames the specified directory or file to the new provided name or path.
     * 
     * @param  string      $oldPath The path of the directory or file to be renamed.
     * @param  string      $newPath The new path or name for the directory or file.
     * @param  string|null $type    Optional type of file to create. Default is null.
     * 
     * @return void               There is no explicit return.
     */
    public static function rename(string $oldPath, string $newPath, string $type = null): void
    {
        // Check if the old path exists
        if (!is_dir($oldPath) && !file_exists($oldPath)) {
            ErrorHandler::handleError('The directory and/or file doesn\'t exist.', 500);
            return;
        }

        // Check if the old path and its parent directory are readable
        if (!is_readable(dirname($oldPath)) || !is_readable($oldPath)) {
            ErrorHandler::handleError('Cannot access the directory and/or file.', 500);
            return;
        }

        // Check if the old path and its parent directory are writable
        if (!is_writable(dirname($oldPath)) || !is_writable($oldPath)) {
            ErrorHandler::handleError('Cannot write to directory and/or file.', 500);
            return;
        }

        // Check if the new path has an extension; if not, sanitize it as a directory
        if (!pathinfo($newPath, PATHINFO_EXTENSION)) {
            $newPath = FileTool::sanitizeDirectory($newPath);
        } else {
            // If the new path has an extension, sanitize it as a file name
            $file = pathinfo($newPath, PATHINFO_FILENAME) . '.' .
            pathinfo($newPath, PATHINFO_EXTENSION);

            $file = FileTool::sanitizeFile($file, $type);
            $newPath = FileTool::sanitizeDirectory(dirname($newPath));

            $newPath = $newPath . '/' . $file;
        }
        // Attempt to rename the old path to the new path
        if (!rename($oldPath, $newPath)) {
            ErrorHandler::handleError('It couldn\'t be renamed.', 500);
            return;
        }
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
        $dir = preg_replace('/[^a-zA-Z0-9\.\/]+/', '', $dir);
        // Normalize directory separators (e.g., // to /)
        $dir = preg_replace('/\/+/', '/', $dir);
        // Replace multiple dots with a single dot
        $dir = preg_replace('/\.+/', '..', $dir);
        // Remove trailing dots at the end of the path
        $dir = preg_replace('/\...$/', '', $dir);
        // Remove trailing slash at the end of the path
        $dir = preg_replace('/\/$/', '', $dir);
        // Return the sanitized directory path
        return $dir;
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
     * Converts all letters in the filename to lower case and
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

    /**
     * Converts a file name to PascalCase format.
     *
     * Converts the filename to PascalCase, capitalizing the first letter of each
     * word and removing whitespace.
     * 
     * @param  string $file The name of the file to be converted.
     * 
     * @return string       The file name is converted to PascalCase.
     */
    private static function toPascal(string $file): string
    {
    	// Convert the file name to title case (PascalCase)
    	$file = mb_convert_case($file, MB_CASE_TITLE, 'UTF-8');
    	// Remove any whitespace from the file name
		$file = preg_replace('/\s+/', '', $file);
		// Extract the file extension and convert it to lowercase
		$fileExtension = pathinfo($file, PATHINFO_EXTENSION);
		$fileExtension = strtolower($fileExtension);
		// Replace the file extension in the filename
		$newFile = str_replace('.' . $fileExtension, '.' . $fileExtension, $file);
		// Return the converted file name
		return $newFile;
    }

    /**
     * Converts a file name to uppercase letters.
     * 
     * Converts all letters in the filename to uppercase and removes blank spaces.
     * 
     * @param  string $file The name of the file to be converted.
     * 
     * @return string       The name of the file converted to upper case.
     */
    private static function toUpper(string $file): string
    {
    	// Convert the file name to uppercase
    	$file = mb_convert_case($file, MB_CASE_UPPER, "UTF-8");
    	// Remove any spaces from the file name
		$file = preg_replace('/\s+/', '', $file);
		// Return the uppercase file name
		return $file;
    }
}
