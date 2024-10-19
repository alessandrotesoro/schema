<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Schema class.
 *
 * @package   Sematico\Baselibs\Schema
 * @author    Alessandro Tesoro <alessandro.tesoro@icloud.com>
 * @copyright 2024 Sematico
 * @license   GPL-3.0-or-later
 */

namespace Sematico\Baselibs\Schema;

/**
 * Handles database schema operations for custom tables.
 *
 * This class provides functionality to define and manage custom database tables,
 * including column definitions, indexes, and foreign keys.
 */
class Schema {

	/**
	 * WordPress database object.
	 *
	 * @var \wpdb
	 */
	private $wpdb;

	/**
	 * Name of the table.
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * Prefix for the table name.
	 *
	 * @var string
	 */
	protected $table_prefix;

	/**
	 * Array of column definitions.
	 *
	 * @var array
	 */
	protected $columns = [];

	/**
	 * Array of index definitions.
	 *
	 * @var array
	 */
	protected $indexes = [];

	/**
	 * Array of foreign key definitions.
	 *
	 * @var array
	 */
	protected $foreign_keys = [];

	/**
	 * Constructor.
	 *
	 * Initializes the Schema object with the given table name and sets up the database connection.
	 *
	 * @param string   $table_name The name of the table without prefix.
	 * @param callable $configure Optional callback to configure the schema.
	 */
	public function __construct( string $table_name, ?callable $configure = null ) {
		global $wpdb;
		$this->wpdb         = $wpdb;
		$this->table_name   = $table_name;
		$this->table_prefix = $wpdb->prefix;

		if ( is_callable( $configure ) ) {
			$configure( $this );
		}
	}

	/**
	 * Get the WordPress database object.
	 *
	 * @return \wpdb The WordPress database object.
	 */
	public function get_wpdb(): \wpdb {
		return $this->wpdb;
	}

	/**
	 * Get the full table name with prefix.
	 *
	 * @return string The full table name including the prefix.
	 */
	public function get_table_name(): string {
		return sprintf( '%s%s', $this->table_prefix, $this->table_name );
	}

	/**
	 * Get the table prefix.
	 *
	 * @return string The table prefix used for this schema.
	 */
	public function get_table_prefix(): string {
		return $this->table_prefix;
	}

	/**
	 * Get the table name without prefix.
	 *
	 * @return string The original table name without the prefix.
	 */
	public function get_table_name_without_prefix(): string {
		return $this->table_name;
	}

	/**
	 * Get the columns defined for the table.
	 *
	 * @return Column[] An array of column definitions. Each element represents a column with its properties.
	 */
	public function get_columns(): array {
		return $this->columns;
	}

	/**
	 * Get a column by its name.
	 *
	 * @param string $name The name of the column.
	 * @return Column The Column object.
	 * @throws Exceptions\ColumnDoesNotExistException If the column does not exist.
	 */
	public function get_column( string $name ): Column {
		if ( ! isset( $this->columns[ $name ] ) ) {
			throw new Exceptions\ColumnDoesNotExistException( $name ); // phpcs:ignore
		}
		return $this->columns[ $name ];
	}

	/**
	 * Get the indexes defined for the table.
	 *
	 * @return Index[] An array of index definitions. Each element represents an index with its properties.
	 */
	public function get_indexes(): array {
		return $this->indexes;
	}

	/**
	 * Define a column for the table.
	 *
	 * @param string $name The name of the column.
	 * @return Column The Column object.
	 */
	public function column( string $name ): Column {
		$column                 = new Column( $name );
		$this->columns[ $name ] = $column;

		return $column;
	}

	/**
	 * Define an index for the table.
	 *
	 * @param string      $column The name of the column to index.
	 * @param string|null $name The name of the index. If null, the name will be generated from the column name.
	 * @return Index The Index object.
	 */
	public function index( string $column, ?string $name = null ): Index {
		$index           = new Index( $column, $name );
		$this->indexes[] = $index;

		return $index;
	}

	/**
	 * Check if a column exists.
	 *
	 * @param string $name The name of the column.
	 * @return bool True if the column exists, false otherwise.
	 */
	public function has_column( string $name ): bool {
		return isset( $this->columns[ $name ] );
	}

	/**
	 * Check if an index exists.
	 *
	 * @param string $column The name of the column.
	 * @return bool True if the index exists, false otherwise.
	 */
	public function has_index( string $column ): bool {
		foreach ( $this->indexes as $index ) {
			if ( $index->get_column() === $column ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Remove a column from the table.
	 *
	 * @param string $name The name of the column to remove.
	 * @return self The Schema object.
	 * @throws Exceptions\ColumnDoesNotExistException If the column does not exist.
	 */
	public function remove_column( string $name ): self {
		if ( ! isset( $this->columns[ $name ] ) ) {
			throw new Exceptions\ColumnDoesNotExistException( $name ); // phpcs:ignore
		}

		unset( $this->columns[ $name ] );

		return $this;
	}

	/**
	 * Remove an index from the table.
	 *
	 * @param string $column The name of the column.
	 * @return self The Schema object.
	 */
	public function remove_index( string $column ): self {
		foreach ( $this->indexes as $key => $index ) {
			if ( $index->get_column() === $column ) {
				unset( $this->indexes[ $key ] );
				break;
			}
		}

		return $this;
	}

	/**
	 * Define a foreign key for the table.
	 *
	 * @param string $column The name of the column to reference.
	 * @param string $name The name of the foreign key.
	 * @return Foreign_Key The Foreign_Key object.
	 */
	public function foreign_key( string $column, ?string $name = null ): Foreign_Key {
		$foreign_key          = new Foreign_Key( $column, $name );
		$this->foreign_keys[] = $foreign_key;

		return $foreign_key;
	}

	/**
	 * Check if there are foreign keys defined.
	 *
	 * @return bool True if there are foreign keys, false otherwise.
	 */
	public function has_foreign_keys(): bool {
		return ! empty( $this->foreign_keys );
	}

	/**
	 * Get the foreign keys defined for the table.
	 *
	 * @return ForeignKey[] An array of foreign key definitions. Each element represents a foreign key with its properties.
	 */
	public function get_foreign_keys(): array {
		return $this->foreign_keys;
	}
}
