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
            throw new \RuntimeException('The directory already exists');
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
            FileTool::createDir($dir, 0777);
        }

        // Check if the directory is readable
        if (!is_readable($dir)) {
            throw new \RuntimeException('Cannot access the directory');
        }

        // Check if the directory is writable
        if (!is_writable($dir)) {
            throw new \RuntimeException('Cannot write to directory');
        }

        // Construct the full path of the file
        $fileFull = $dir . '/' . $file;

        // Check if the file already exists
        if (file_exists($fileFull)) {
            throw new \RuntimeException('The file already exists.');
        }

        // Attempt to create the file
        if (!fopen($fileFull, 'w')) {
            throw new \RuntimeException('The file could not be created');
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
     * @param string      $dir      The path of the directory where the files will
     *                              be created.
     * @param string      $file     The base name of the files to be created.
     * @param string|null $type     Optional type of files to be created. Default
     *                              is null.
     * @param int         $quantity The number of files to be created.
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
            throw new \InvalidArgumentException('The quantity cannot be 0 and/or negative.');
        }

        // Create the directory if it doesn't exist
        if (!is_dir($dir)) {
            FileTool::createDir($dir, 0777);
        }

        // Check if the directory is readable
        if (!is_readable($dir)) {
            throw new \RuntimeException('Cannot access the directory');
        }

        // Check if the directory is writable
        if (!is_writable($dir)) {
            throw new \RuntimeException('Cannot write to directory');
        }

        // Construct the full path of the file
        $fileFull = $dir . '/' . $file;
        // Extract the file extension and file name without extension
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        $fileName = pathinfo($file, PATHINFO_FILENAME);

        // Check if the base file already exists
        if (file_exists($fileFull)) {
            throw new \RuntimeException('The file already exists.');
        }

        // Create the specified number of files with sequential names
        for ($i = 1; $i <= $quantity; $i++) {
            // Attempt to create the file
            if (!fopen($fileFull, 'w')) {
                throw new \RuntimeException('The file could not be created');
            }
            // Generate the next file name for the next iteration
            $fileFull = $dir . '/' . $fileName . '_' . $i . '.' . $fileExtension;
        }

        // Clear stat cache
        clearstatcache();
    }

    /**
     * Copies a file to a new location.
     *
     * Copies the file from the specified origin to the provided destination.
     * If the destination directory doesn't exist, it will be created. If a file
     * with the same name already exists in the destination directory, a unique name
     * will be generated by appending a numerical suffix.
     *
     * @param string $origin  The path of the file to be copied.
     * @param string $destiny The destination path where the file will be copied.
     *
     * @return void            There is no explicit return.
     */
    public static function copy(string $origin, string $destiny): void
    {
        // Check if the origin directory and file exist
        if (!is_dir(dirname($origin)) || !file_exists($origin)) {
           throw new \RuntimeException('The directory and/or file doesn\'t exist.');
        }

        // Check if the origin directory is writable
        if (!is_writable(dirname($origin))) {
            throw new \RuntimeException('Cannot write to directory.');
        }

        // Check if the origin file is readable
        if (!is_readable($origin)) {
            throw new \RuntimeException('Cannot access the file.');
        }

        // Sanitize the destination directory path
        $destiny = FileTool::sanitizeDirectory($destiny);

        // Create the destination directory if it doesn't exist
        if (!is_dir($destiny)) {
            FileTool::createDir($destiny, 0777);
        }

        // Initialize counter for generating unique file names
        $i = 1;
        // Extract file name and extension
        $fileName = pathinfo(basename($origin), PATHINFO_FILENAME);
        $fileExtension = pathinfo(basename($origin), PATHINFO_EXTENSION);

        // Open the destination directory
        if ($dir = opendir($destiny)) {
            // Iterate through files in the destination directory
            while (($file = readdir($dir)) !== false) {
                // Exclude current directory (.) and parent directory (..)
                if ($file != '.' && $file != '..') {
                    // Check if a file with the same name already exists
                    if (file_exists($destiny . '/' . basename($origin))) {
                        // Generate a unique file name
                        $newFile = $fileName . '(' . $i . ')' . '.' . $fileExtension;
                        $i++;
                    }
                }
            }
            // Close the destination directory
            closedir($dir);
        }

        // Copy the file to the destination with a unique name if necessary
        if (isset($newFile)) {
            copy($origin, $destiny . '/' . $newFile);
        } else {
            copy($origin, $destiny . '/' . basename($origin));
        }
    }

    /**
     * Copies all files from one directory to another.
     *
     * Copies all files from the specified origin directory to the provided
     * destination directory. If the destination directory doesn't exist, it will be
     * created. Existing files in the destination directory will not be overwritten.
     *
     * @param string $origin  The path of the directory containing the files to be
     *                        copied.
     * @param string $destiny The destination directory where the files will be
     *                        copied.
     *
     * @return void          There is no explicit return.
     */
    public static function copyAll(string $origin, string $destiny): void
    {
        // Check if the origin directory exists
        if (!is_dir($origin)) {
            throw new \RuntimeException('The directory doesn\'t exist.');
        }

        // Check if the origin directory is writable
        if (!is_writable($origin)) {
           throw new \RuntimeException('Cannot write to directory.');
        }

        // Sanitize the destination directory path
        $destiny = FileTool::sanitizeDirectory($destiny);

        // Create the destination directory if it doesn't exist
        if (!is_dir($destiny)) {
            FileTool::createDir($destiny, 0777);
        }

        // Get list of files in the origin directory
        $files = scandir($origin);
        // Initialize counter for files that couldn't be copied
        $countFiles = 0;

        // Iterate through each file in the origin directory
        foreach ($files as $file) {
            // Check if the item is a file
            if (is_file($origin . '/' . $file)) {
                // Check if the file is readable and doesn't exist in the destination
                if (is_readable($origin . '/' . $file) && !file_exists($destiny . '/' . $file)) {
                    // Copy the file to the destination directory
                    copy($origin . '/' . $file, $destiny . '/' . $file);
                } else {
                    // Increment counter if the file couldn't be copied
                    $countFiles++;
                }
            }
        }

        // If there are files that couldn't be copied, handle the error
        if ($countFiles > 0) {
            throw new \RuntimeException("$countFiles files cannot be copied.");
        }
    }

    /**
     * Copies the content of one file to another.
     *
     * Copies the content of the source file to the destination file. If the
     * destination file does not exist, it will be created. If the destination file
     * already exists, its content will be overwritten.
     *
     * @param string $fileOrigin  The path of the source file.
     * @param string $fileDestiny The path of the destination file.
     *
     * @return void              There is no explicit return.
     */
    public static function copyContent(string $fileOrigin, string $fileDestiny): void
    {
        // Sanitize fileOrigin paths
        $fileOrigin = FileTool::sanitizeDirectory(dirname($fileOrigin)) . '/' .
                      FileTool::sanitizeFile(basename($fileOrigin));

        // Sanitize fileDestiny paths
        $fileDestiny = FileTool::sanitizeDirectory(dirname($fileDestiny)) . '/' .
                       FileTool::sanitizeFile(basename($fileDestiny));

        // Check if the source file and its directory exist
        if (!file_exists($fileOrigin) || !is_dir(dirname($fileOrigin))) {
            throw new \RuntimeException('The source file and/or directory does not exist.');
        }

        // Check if the source file and its directory are readable
        if (!is_readable($fileOrigin) || !is_readable(dirname($fileOrigin))) {
            throw new \RuntimeException('The source file and/or directory is not accessible.');
        }

        // Create the directory for the destination file if it doesn't exist
        if (!is_dir(dirname($fileDestiny))) {
            FileTool::createDir(dirname($fileDestiny));
        }

        // Create the destination file if it doesn't exist
        if (!file_exists($fileDestiny)) {
            FileTool::createFile(dirname($fileDestiny), basename($fileDestiny));
        }

        // Check if the destination file and its directory are writable
        if (!is_writable($fileDestiny) || !is_writable(dirname($fileDestiny))) {
            throw new \RuntimeException(
                'The target file and/or directory does not have write permission.'
            );
        }

        // Read the content of the source file
        $content = file_get_contents($fileOrigin);

        // Check if content is successfully read
        if ($content === false) {
            throw new \RuntimeException('Error reading the contents of the source file.');
        }

        // Open the destination file for writing
        if (!$file = fopen($fileDestiny, 'w')) {
            throw new \RuntimeException('Error opening the destination file for writing.');
        }

        // Write the content to the destination file
        fwrite($file, $content);
        // Close the destination file
        fclose($file);
    }

    /**
     * Purges a directory.
     *
     * Recursively deletes all files and subdirectories within the specified
     * directory.
     *
     * @param string $dir The path of the directory to purge.
     *
     * @return void      No return value.
     */
    public static function purge(string $dir): void
    {
        // Sanitize the directory path
        $dir = FileTool::sanitizeDirectory($dir);

        // Check if the directory exists
        if (!is_dir($dir)) {
            throw new \RuntimeException('The directory doesn\'t exist.');
        }

        // Check if the directory is writable
        if (!is_writable($dir)) {
            throw new \RuntimeException('The directory doesn\'t have write permission.');
        }

        // Get the list of files and directories in the directory
        $items = scandir($dir);
        // Remove '.' and '..' from the list of files
        $items = array_diff($items, ['.','..']);

        // Iterate over each file and subdirectory
        foreach ($items as $item) {
            // Create the full path to the item
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            // Recursively purge subdirectories
            if (is_dir($path)) {
                self::purge($path);
            } else {
                // Check if the file is writable before deletion
                if (!is_writable($path)) {
                    throw new \RuntimeException('There are files that cannot be deleted.');
                }
                // Delete the file
                unlink($path);
            }
        }
        // Remove the directory itself after all its contents are deleted
        rmdir($dir);
    }

    /**
     * Reads content from a file.
     *
     * Reads the content of the specified file and returns it as a string.
     *
     * @param string $dir The path of the file to read from.
     *
     * @return string      The content of the file as a string.
     */
    public static function read(string $dir): string
    {
        // Sanitize file path
        $dir = FileTool::sanitizeDirectory(dirname($dir)) . '/' .
               FileTool::sanitizeFile(basename($dir));

        // Check if the directory exists
        if (!is_dir(dirname($dir))) {
            throw new \RuntimeException('The directory doesn\'t exist.');
        }

        // Check if the directory is writable
        if (!is_writable(dirname($dir))) {
            throw new \RuntimeException('Cannot write to directory');
        }

        // Check if the file exists
        if (!file_exists($dir)) {
            throw new \RuntimeException('The file doesn\'t exist.');
        }

        // Check if the file is readable
        if (!is_readable($dir)) {
            throw new \RuntimeException('The file doesn\'t have read permission.');
        }

        // Open the file for reading
        if (!$file = fopen($dir, 'r')) {
            throw new \RuntimeException('The file couldn\'t be opened.');
        }

        // Read the content of the file
        $content = fread($file, filesize($dir));
        // Close the file
        fclose($file);
        // Return the content of the file
        return $content;
    }

    /**
     * Removes an empty directory.
     *
     * Removes the specified directory if it is empty.
     *
     * @param string $dir The path of the directory to be removed.
     *
     * @return void      There is no explicit feedback.
     */
    public static function removeDir(string $dir): void
    {
        // Check if the directory exists
        if (!is_dir($dir)) {
            throw new \RuntimeException('The directory doesn\'t exist.');
        }

        // Check if the parent directory is readable
        if (!is_readable(dirname($dir))) {
            throw new \RuntimeException('Cannot access the directory.');
        }

        // Check if the parent directory is writable
        if (!is_writable(dirname($dir))) {
            throw new \RuntimeException('Cannot write to directory.');
        }

        // Check if the directory is empty
        if (count(scandir($dir)) !== 2) {
            throw new \RuntimeException('The directory is not empty.');
        }

        // Remove the directory
        rmdir($dir);
    }

    /**
     * Remove a file.
     *
     * Removes the specified file, if it exists.
     *
     * @param string $path The path of the file to be removed.
     *
     * @return void       There is no explicit feedback.
     */
    public static function removeFile(string $path): void
    {
        // Check if the directory of the file exists
        if (!is_dir(dirname($path))) {
            throw new \RuntimeException('The directory doesn\'t exist.');
        }

        // Check if the parent directory is readable
        if (!is_readable(dirname($path))) {
            throw new \RuntimeException('Cannot access the directory.');
        }

        // Check if the parent directory is writable
        if (!is_writable(dirname($path))) {
            throw new \RuntimeException('Cannot write to directory.');
        }

        // Check if the file exists
        if (!file_exists($path)) {
            throw new \RuntimeException('The file doesn\'t exist.');
        }

        // Check if it's a regular file
        if (!is_file($path)) {
            throw new \RuntimeException('It\'s not a file.');
        }

        // Check if the file is writable
        if (!is_writable($path)) {
            throw new \RuntimeException('The file cannot be deleted because it is not writable.');
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
     * @param string $dir The path of the directory to be removed.
     *
     * @return void      There is no explicit feedback.
     */
    public static function removeAll(string $dir): void
    {
        // Check if the directory exists
        if (!is_dir($dir)) {
            throw new \RuntimeException('The directory doesn\'t exist.');
        }

        // Check if the parent directory is readable
        if (!is_readable(dirname($dir))) {
            throw new \RuntimeException('Cannot access the directory.');
        }

        // Check if the parent directory is writable
        if (!is_writable(dirname($dir))) {
            throw new \RuntimeException('Cannot write to directory.');
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
            throw new \RuntimeException("$countFiles files could not be deleted.");
        }

        // Remove the directory
        FileTool::removeDir($dir);
    }

    /**
     * Renames a directory or file.
     *
     * Renames the specified directory or file to the new provided name or path.
     *
     * @param string      $oldPath The path of the directory or file to be renamed.
     * @param string      $newPath The new path or name for the directory or file.
     * @param string|null $type    Optional type of file to create. Default is null.
     *
     * @return void               There is no explicit return.
     */
    public static function rename(string $oldPath, string $newPath, string $type = null): void
    {
        // Check if the old path exists
        if (!is_dir($oldPath) && !file_exists($oldPath)) {
            throw new \RuntimeException('The directory and/or file doesn\'t exist.');
        }

        // Check if the old path and its parent directory are readable
        if (!is_readable(dirname($oldPath)) || !is_readable($oldPath)) {
            throw new \RuntimeException('Cannot access the directory and/or file.');
        }

        // Check if the old path and its parent directory are writable
        if (!is_writable(dirname($oldPath)) || !is_writable($oldPath)) {
            throw new \RuntimeException('Cannot write to directory and/or file.');
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
            throw new \RuntimeException('It couldn\'t be renamed.');
        }
    }

    /**
     * Renames all files in a directory with a common base name and sequential
     * numbering.
     *
     * Renames all files within the specified directory using the provided base
     * file name and assigns sequential numbering to each file. Optionally, a file
     * type can be specified.
     *
     * @param string      $dir      The path of the directory containing the files
     *                              to be renamed.
     * @param string      $fileName The base name for the files to be renamed.
     * @param string|null $type     Optional type of the files to be renamed.
     *                              Default is null.
     *
     * @return void                There is no explicit return.
     */
    public static function renameAll(string $dir, string $fileName, string $type = null): void
    {
        // Check if the directory exists
        if (!is_dir($dir)) {
            throw new \RuntimeException('The directory doesn\'t exist.');
        }

        // Check if the directory is readable
        if (!is_readable($dir)) {
            throw new \RuntimeException('Cannot access the directory');
        }

        // Check if the directory is writable
        if (!is_writable($dir)) {
            throw new \RuntimeException('Cannot write to directory');
        }

        // Sanitize the new base file name
        $fileName = FileTool::sanitizeFile($fileName, $type);

        // Extract the file extension and file name without extension
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);

        $countFiles = 0;
        $version = 1;

        // Open the directory handle
        if ($dh = opendir($dir)) {
            // Iterate through the files in the directory
            while (($file = readdir($dh)) !== false) {
                // Exclude current directory (.) and parent directory (..)
                if ($file != '.' && $file != '..') {
                    // Generate the new file name with version number
                    $newFile = $fileName . '_' . $version . '.' . $fileExtension;
                    // Check if the file is readable and writable
                    if (!is_readable($dir . '/' . $file) || !is_writable($dir . '/' . $file)) {
                        $countFiles++;
                    } else {
                        // Rename the file
                        rename($dir . '/' . $file, $dir . '/' . $newFile);
                    }
                    // Increment the version number for the next file
                    $version++;
                }
            }
            // Close the directory handle
            closedir($dh);
        }
        // If there are files that couldn't be renamed, handle the error
        if ($countFiles > 0) {
            throw new \RuntimeException("$countFiles files cannot be renamed.");
        }
    }

    /**
     * Retrieves a list of files in a directory.
     *
     * Retrieves an array containing the names of files within the specified
     * directory. The function excludes the '.' and '..' directories from the list.
     *
     * @param string $dir The path of the directory to retrieve files from.
     *
     * @return array           An array of file names.
     */
    public static function show(string $dir): array
    {
        // Sanitize directory path
        $dir = FileTool::sanitizeDirectory($dir);

        // Check if the directory exists
        if (!is_dir($dir)) {
            throw new \RuntimeException('The directory doesn\'t exist.');
        }

        // Check if the directory is readable
        if (!is_readable($dir)) {
            throw new \RuntimeException('Cannot access the directory');
        }

        // Get the list of files in the directory
        $files = scandir($dir);
        // Exclude '.' and '..' directories from the list
        $files = array_diff($files, ['..', '.']);
        // Returns an array with the files
        return $files;
    }

    /**
     * Writes content to a file.
     *
     * Writes the specified content to a file. The method allows for appending
     * content to an existing file or overwriting its contents based on the value
     * of the $overwrite parameter.
     *
     * @param string      $dir       The path of the file to write to.
     * @param string      $content   The content to write to the file.
     * @param int|integer $overwrite Determines whether to overwrite the file's
     *                               content (1) or append to it (0). Default is 0.
     *
     * @return void                 There is no explicit return value.
     */
    public static function write(string $dir, string $content, int $overwrite = 0): void
    {
        // Sanitize file path
        $dir = FileTool::sanitizeDirectory(dirname($dir)) . '/' .
               FileTool::sanitizeFile(basename($dir));

        // Check if the file and its directory exist
        if (!is_dir(dirname($dir)) || !file_exists($dir)) {
            throw new \RuntimeException('The file and/or directory doesn\'t exist');
        }

        // Check if the file and its directory are writable
        if (!is_writable(dirname($dir)) || !is_writable($dir)) {
            throw new \RuntimeException('The file and/or directory do not have write permission.');
        }

        // Determine whether to append or overwrite content based on the $overwrite
        // parameter
        if ($overwrite === 0) {
            // Append content to the file
            if ($file = fopen($dir, 'a')) {
                fwrite($file, $content);
            } else {
                // Handle error if the file couldn't be opened for appending
                throw new \RuntimeException('The file couldn\'t be opened.');
            }
        } elseif ($overwrite === 1) {
            // Overwrite content of the file
            if ($file = fopen($dir, 'w')) {
                fwrite($file, $content);
            } else {
                // Handle error if the file couldn't be opened for writing
                throw new \RuntimeException('The file couldn\'t be opened.');
            }
        } else {
            // Handle error if the value of $overwrite is invalid
            throw new \InvalidArgumentException('The overwrite parameter only accepts 0 or 1.');
        }

        // Close the file
        fclose($file);
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
     * @return string            The sanitized file name.
     */
    private static function sanitizeFile(string $file, string $type = null): string
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
                // If an invalid type is provided, throw an exception
                throw new \InvalidArgumentException('The following pattern does not exist.');
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
     * @param string $file The name of the file to be converted.
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
     * @param string $file The name of the file to be converted.
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
