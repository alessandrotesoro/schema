<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Default mysql builder implementation.
 *
 * @package   Sematico\Baselibs\Schema
 * @author    Alessandro Tesoro <alessandro.tesoro@icloud.com>
 * @copyright 2024 Sematico
 * @license   GPL-3.0-or-later
 */

namespace Sematico\Baselibs\Schema\Builder;

use Sematico\Baselibs\Schema\Schema;
use Sematico\Baselibs\Schema\Grammar\Grammar;
use Sematico\Baselibs\Schema\Validator;

/**
 * Default mysql builder implementation.
 */
class Builder implements BuilderInterface {

	/**
	 * Create a table based on the provided schema.
	 *
	 * @param Schema $schema The schema object defining the table structure.
	 * @return bool True on success, false on failure.
	 */
	public function create_table( Schema $schema ): bool {
		$validator = new Validator( $schema );

		if ( ! $validator->validate() ) {
			return false;
		}

		$grammar = new Grammar();
		$sql     = $grammar->compile( $schema );
		$result  = $schema->get_wpdb()->query( $sql );

		return $result !== false;
	}

	/**
	 * Drop a table based on the provided schema.
	 *
	 * @param Schema $schema The schema object defining the table structure.
	 * @return bool True on success, false on failure.
	 */
	public function drop_table( Schema $schema ): bool {
		$table_name = $schema->get_table_name();
		$sql        = sprintf( 'DROP TABLE IF EXISTS %s;', $table_name );
		$result     = $schema->get_wpdb()->query( $sql );

		return $result !== false;
	}

	/**
	 * Truncate a table.
	 *
	 * @param string $table_name The name of the table without prefix.
	 * @return bool True on success, false on failure.
	 */
	public function truncate_table( string $table_name ): bool {
		global $wpdb;

		$full_table_name = $wpdb->prefix . $table_name;
		$result          = $wpdb->query( $wpdb->prepare( 'TRUNCATE TABLE %s;', $full_table_name ) );

		return $result !== false;
	}
}
