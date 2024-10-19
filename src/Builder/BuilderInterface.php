<?php // phpcs:ignore WordPress.Files.FileName
/**
 * BuilderInterface interface
 *
 * @package   Sematico\Baselibs\Schema
 * @author    Alessandro Tesoro <alessandro.tesoro@icloud.com>
 * @copyright 2024 Sematico
 * @license   GPL-3.0-or-later
 */

namespace Sematico\Baselibs\Schema\Builder;

use Sematico\Baselibs\Schema\Schema;

/**
 * This interface defines the methods required for creating and dropping tables
 * based on a provided schema.
 */
interface BuilderInterface {

	/**
	 * Create a table based on the provided schema.
	 *
	 * @param Schema $schema The schema object defining the table structure.
	 * @return bool True on success, false on failure.
	 */
	public function create_table( Schema $schema ): bool;

	/**
	 * Drop a table based on the provided schema.
	 *
	 * @param Schema $schema The schema object defining the table structure.
	 * @return bool True on success, false on failure.
	 */
	public function drop_table( Schema $schema ): bool;

	/**
	 * Truncate a table.
	 *
	 * @param string $table_name The name of the table without prefix.
	 * @return bool True on success, false on failure.
	 */
	public function truncate_table( string $table_name ): bool;

}
