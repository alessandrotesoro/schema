<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Validator class
 *
 * @package   Sematico\Baselibs\Schema
 * @author    Alessandro Tesoro <alessandro.tesoro@icloud.com>
 * @copyright 2024 Sematico
 * @license   GPL-3.0-or-later
 */

namespace Sematico\Baselibs\Schema;

/**
 * This class is responsible for validating the schema definitions, ensuring that all columns and indexes
 * meet the required criteria. It checks for unique column names, defined column types, and valid index definitions.
 */
class Validator {

	/**
	 * Schema instance
	 *
	 * @var Schema
	 */
	protected Schema $schema;

	/**
	 * Constructor
	 *
	 * @param Schema $schema Schema instance.
	 */
	public function __construct( Schema $schema ) {
		$this->schema = $schema;
	}

	/**
	 * Ensure all defined columns have unique names and have types defined.
	 *
	 * @return bool True if all columns are valid, false otherwise.
	 * @throws \Exception If a column name is not unique or a column type is not defined.
	 */
	public function validate_columns(): bool {
		$column_names = [];
		foreach ($this->schema->get_columns() as $column) {
			if (in_array( $column->get_name(), $column_names, true )) {
				throw new \Exception( sprintf( 'Duplicate column name found: %s', esc_html( $column->get_name() ) ) );
			}
			$column_names[] = $column->get_name();

			if (empty( $column->get_type() )) {
				throw new \Exception( sprintf( 'Column type not defined for column: %s', esc_html( $column->get_name() ) ) );
			}
		}
		return true;
	}

	/**
	 * Ensure all defined indexes are valid.
	 *
	 * @return bool True if all indexes are valid, false otherwise.
	 * @throws \Exception If an index is not valid.
	 */
	public function validate_indexes(): bool {
		foreach ( $this->schema->get_indexes() as $index ) {
			if ( empty( $index->get_column() ) ) {
				throw new \Exception( sprintf( 'Index column not defined for index: %s', esc_html( $index->get_name() ) ) );
			}
		}
		return true;
	}

	/**
	 * Ensure all defined indexes have a column.
	 *
	 * @return bool True if all indexes have a column, false otherwise.
	 * @throws \Exception If an index does not have a column.
	 */
	public function validate_indexes_have_column(): bool {
		foreach ( $this->schema->get_indexes() as $index ) {
			if ( ! $this->schema->has_column( $index->get_column() ) ) {
				throw new \Exception( sprintf( 'Index does not have a valid column defined: %s', esc_html( $index->get_name() ) ) );
			}
		}
		return true;
	}

	/**
	 * Ensure that only a single primary key has been defined.
	 *
	 * @return bool True if only one primary key is defined, false otherwise.
	 * @throws \Exception If more than one primary key is defined.
	 */
	public function validate_primary_key(): bool {
		$primary_key_count = 0;
		foreach ( $this->schema->get_indexes() as $index ) {
			if ( $index->is_primary() ) {
				++$primary_key_count;
			}
		}

		if ( $primary_key_count > 1 ) {
			throw new \Exception( 'Multiple primary keys defined.' );
		}

		return true;
	}

	/**
	 * Ensure columns that are part of a primary key are not nullable.
	 *
	 * @return bool True if all primary key columns are not nullable, false otherwise.
	 * @throws \Exception If a primary key column is nullable.
	 */
	public function validate_primary_key_nullability(): bool {
		foreach ( $this->schema->get_indexes() as $index ) {
			if ( $index->is_primary() ) {
				$column = $this->schema->get_column( $index->get_column() );
				if ( $column->is_nullable() ) {
					throw new \Exception( sprintf( 'Primary key column cannot be nullable: %s', esc_html( $column->get_name() ) ) );
				}
			}
		}
		return true;
	}

	/**
	 * Ensure an auto-increment column is set as the primary index.
	 *
	 * @return bool True if the auto-increment column is set as the primary index, false otherwise.
	 * @throws \Exception If an auto-increment column is not set as the primary index.
	 */
	public function validate_auto_increment_primary_key(): bool {
		foreach ( $this->schema->get_columns() as $column ) {
			if ( $column->is_auto_increment() ) {
				foreach ( $this->schema->get_indexes() as $index ) {
					if ( $index->is_primary() && $index->get_column() === $column->get_name() ) {
						return true;
					}
				}
				throw new \Exception( sprintf( 'Auto-increment column is not set as primary index: %s', esc_html( $column->get_name() ) ) );
			}
		}
		return true;
	}

	/**
	 * Validate the schema.
	 *
	 * @return bool True if the schema is valid, false otherwise.
	 * @throws \Exception If the schema is not valid.
	 */
	public function validate(): bool {
		return $this->validate_columns() &&
			$this->validate_indexes() &&
			$this->validate_indexes_have_column() &&
			$this->validate_primary_key() &&
			$this->validate_primary_key_nullability() &&
			$this->validate_auto_increment_primary_key();
	}
}
