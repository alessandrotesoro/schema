<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Foreign Key definition class.
 *
 * @package   Sematico\Baselibs\Schema
 * @author    Alessandro Tesoro <alessandro.tesoro@icloud.com>
 * @copyright 2024 Sematico
 * @license   GPL-3.0-or-later
 */

namespace Sematico\Baselibs\Schema;

/**
 * Class Foreign_Key
 *
 * Represents a foreign key in a database schema.
 */
class Foreign_Key {
	/**
	 * Foreign key name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Foreign key column.
	 *
	 * @var string
	 */
	protected $column;

	/**
	 * Foreign key reference table.
	 *
	 * @var string
	 */
	protected $reference_table;

	/**
	 * Foreign key reference column.
	 *
	 * @var string
	 */
	protected $reference_column;

	/**
	 * Foreign key on delete action.
	 *
	 * @var string
	 */
	protected $on_delete = 'NO ACTION';

	/**
	 * Foreign key on update action.
	 *
	 * @var string
	 */
	protected $on_update = 'NO ACTION';

	/**
	 * Constructor for Foreign_Key.
	 *
	 * @param string $column The column that is the foreign key.
	 * @param string $name The name of the foreign key.
	 */
	public function __construct( string $column, ?string $name = null ) {
		$this->name   = $name ?? 'fk_' . $column;
		$this->column = $column;
	}

	/**
	 * Set the reference table and column.
	 *
	 * @param string $reference_table The table that is referenced.
	 * @param string $reference_column The column that is referenced.
	 * @return self
	 */
	public function reference( string $reference_table, string $reference_column ): self {
		$this->reference_table  = $reference_table;
		$this->reference_column = $reference_column;
		return $this;
	}

	/**
	 * Set the reference table.
	 *
	 * @param string $reference_table The table that is referenced.
	 * @return self
	 */
	public function reference_table( string $reference_table ): self {
		$this->reference_table = $reference_table;
		return $this;
	}

	/**
	 * Set the reference column.
	 *
	 * @param string $reference_column The column that is referenced.
	 * @return self
	 */
	public function reference_column( string $reference_column ): self {
		$this->reference_column = $reference_column;
		return $this;
	}

	/**
	 * Set the on delete action.
	 *
	 * @param string $on_delete The action to take when the referenced row is deleted.
	 * @return self
	 */
	public function on_delete( string $on_delete ): self {
		$this->on_delete = $on_delete;
		return $this;
	}

	/**
	 * Set the on update action.
	 *
	 * @param string $on_update The action to take when the referenced row is updated.
	 * @return self
	 */
	public function on_update( string $on_update ): self {
		$this->on_update = $on_update;
		return $this;
	}

	/**
	 * Get the foreign key name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Get the foreign key column.
	 *
	 * @return string
	 */
	public function get_column(): string {
		return $this->column;
	}

	/**
	 * Get the reference table.
	 *
	 * @return string
	 */
	public function get_reference_table(): string {
		return $this->reference_table;
	}

	/**
	 * Get the reference column.
	 *
	 * @return string
	 */
	public function get_reference_column(): string {
		return $this->reference_column;
	}

	/**
	 * Get the on delete action.
	 *
	 * @return string
	 */
	public function get_on_delete(): string {
		return $this->on_delete;
	}

	/**
	 * Get the on update action.
	 *
	 * @return string
	 */
	public function get_on_update(): string {
		return $this->on_update;
	}
}
