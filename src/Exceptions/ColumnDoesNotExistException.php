<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Column does not exist exception class.
 *
 * @package   Sematico\Baselibs\Schema
 * @author    Alessandro Tesoro <alessandro.tesoro@icloud.com>
 * @copyright 2024 Sematico
 * @license   GPL-3.0-or-later
 */

namespace Sematico\Baselibs\Schema\Exceptions;

use Exception;

/**
 * Exception thrown when a specified column does not exist.
 */
class ColumnDoesNotExistException extends Exception {

	/**
	 * Constructor for the ColumnDoesNotExistException class.
	 *
	 * @param string    $column_name The name of the column that does not exist.
	 * @param int       $code        The exception code (optional).
	 * @param Exception $previous    The previous exception used for exception chaining (optional).
	 */
	public function __construct( $column_name, $code = 0, Exception $previous = null ) {
		$message = "The column '{$column_name}' does not exist.";
		parent::__construct( $message, $code, $previous );
	}
}
