<?php

namespace PHPFileTool\FileTool;

/**
 * FileTool - 
 * 
 * @category File Utilities
 * @package PHPFileTool\FileTool
 * @author XxZeroxX <antoniomarcos.silva@protonmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 *          v3.0 or later
 * @link https://antoniosilva.hashnode.dev
 * @since Release: 1.0
 */
class FileTool
{
	/**
	 * Method for creating directories.
	 * 
	 * Creates a directory with the specified path, if it doesn't already exist.
	 * 
	 * @param  string      $dir       The path of the directory to be created.
	 * @param  int|integer $permisson Directory permissions to be applied. 
	 *                                The default is 0777.
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
	 * Method for sanitizing directory names.
	 * 
	 * Removes special characters and normalizes the format of a directory path.
	 * 
	 * @param  string $dir The path of the directory to be sanitized.
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
}
