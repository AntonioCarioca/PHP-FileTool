<?php

namespace PHPFileTool\FileTool;

/**
 * ErrorHandler - Class responsible for handling errors and returning an
 * appropriate response.
 *
 * This class provides a static method for handling HTTP 500 errors,
 * providing an appropriate response and recording the error in the server log.
 * 
 * @category Utility
 * @package  PHPFileTool\FileTool
 * @author   XxZeroxX <antoniomarcos.silva@protonmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 *           v3.0 or later
 * @link     https://antoniosilva.hashnode.dev
 * @since    Release: 1.0
 */
class ErrorHandler
{
    /**
     * Static method for dealing with errors.
     *
     * This method receives an error message and an error code,
     * sets the HTTP header to indicate a 500 error, displays a custom message
     * in the response body and records the error in the server's error log.
     *
     * @param string $errorMessage The error message.
     * @param int    $errorCode    The error code.
     * 
     * @return void
     */
    public static function handleError(string $errorMessage, int $errorCode): void
    {
        // Set the HTTP header for a 500 error
        header('HTTP/1.0 500 Internal Server Error');

        // Displays a customized error message in the response body
        echo 'Internal Server Error - Custom Handler <br>';
        echo "Error [$errorCode]: $errorMessage";

        // Record the error in the server error log
        error_log("Error [$errorCode]: $errorMessage");
    }
}
