<?php

namespace Sematico\Baselibs\Schema\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base test case class. Mainly used for integration tests.
 */
abstract class TestCase extends BaseTestCase {
	/**
	 * Helper function to get column information.
	 *
	 * @param string $column_name The name of the column.
	 * @return object|false The column information or false if not found.
	 */
	public function getColumnInfo( $column_name ) {
		$table_name = $this->schema->get_table_name();
		$query      = $this->wpdb->prepare(
			"SHOW COLUMNS FROM {$table_name} WHERE Field = %s",
			$column_name
		);

		ray( $query );

		return $this->wpdb->get_row( $query );
	}

	/**
	 * Get the table indexes.
	 *
	 * @return array The table indexes.
	 */
	public function getTableIndexes() {
		global $wpdb;
		$table_name = $this->schema->get_table_name();
		$indexes    = $wpdb->get_results( "SHOW INDEX FROM `$table_name`", ARRAY_A );

		$formatted_indexes = [];
		foreach ($indexes as $index) {
			$formatted_indexes[ $index['Key_name'] ] = [
				'Column_name' => $index['Column_name'],
				'Key_name'    => $index['Key_name'],
				'Non_unique'  => $index['Non_unique'],
				'Index_type'  => $index['Index_type'],
			];
		}

		return $formatted_indexes;
	}

	/**
	 * Check if a table exists.
	 *
	 * @param string $table_name The name of the table.
	 * @return bool True if the table exists, false otherwise.
	 */
	public function tableExists( $table_name ) {
		global $wpdb;
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name );
		return $wpdb->get_var( $query ) === $table_name;
	}

	/**
	 * Get the foreign keys.
	 *
	 * @param string $table_name The name of the table.
	 * @return array The foreign keys.
	 */
	public function getForeignKeys( $table_name ) {
		global $wpdb;
		$query = $wpdb->prepare(
			'
			SELECT
				kcu.CONSTRAINT_NAME,
				kcu.COLUMN_NAME,
				kcu.REFERENCED_TABLE_NAME,
				kcu.REFERENCED_COLUMN_NAME,
				rc.DELETE_RULE,
				rc.UPDATE_RULE
			FROM information_schema.KEY_COLUMN_USAGE kcu
			JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
				ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
			WHERE kcu.REFERENCED_TABLE_SCHEMA = %s
			AND kcu.TABLE_NAME = %s
			AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
		',
			DB_NAME,
			$table_name
		);

		return $wpdb->get_results( $query );
	}
}
