<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Index definition class.
 *
 * @package   Sematico\Baselibs\Schema
 * @author    Alessandro Tesoro <alessandro.tesoro@icloud.com>
 * @copyright 2024 Sematico
 * @license   GPL-3.0-or-later
 */

namespace Sematico\Baselibs\Schema;

/**
 * Class Index
 *
 * Represents an index in a database schema.
 */
class Index {
	/**
	 * Index name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Column name.
	 *
	 * @var string
	 */
	protected $column;

	/**
	 * Whether the index is unique.
	 *
	 * @var bool
	 */
	protected $unique = false;

	/**
	 * Whether the index is full-text.
	 *
	 * @var bool
	 */
	protected $full_text = false;

	/**
	 * Whether the index is primary.
	 *
	 * @var bool
	 */
	protected $primary = false;

	/**
	 * Constructor.
	 *
	 * @param string      $column Column name.
	 * @param string|null $name Index name. If null, a default name will be generated.
	 */
	public function __construct( string $column, string $name = null ) {
		$this->name   = $name ?? 'idx_' . $column;
		$this->column = $column;
	}

	/**
	 * Get the index name.
	 *
	 * @return string The name of the index.
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Get the column name.
	 *
	 * @return string The name of the column this index is applied to.
	 */
	public function get_column(): string {
		return $this->column;
	}

	/**
	 * Checks if using a unique index.
	 *
	 * @return bool True if the index is unique, false otherwise.
	 */
	public function is_unique(): bool {
		return $this->unique;
	}

	/**
	 * Checks if using a full text index.
	 *
	 * @return bool True if the index is full-text, false otherwise.
	 */
	public function is_full_text(): bool {
		return $this->full_text;
	}

	/**
	 * Checks if using a primary key.
	 *
	 * @return bool True if the index is a primary key, false otherwise.
	 */
	public function is_primary(): bool {
		return $this->primary;
	}

	/**
	 * Get the index type.
	 *
	 * @return string The type of the index: 'PRIMARY KEY', 'UNIQUE', 'FULLTEXT', or 'INDEX'.
	 */
	public function get_type(): string {
		if ( $this->primary ) {
			return 'PRIMARY KEY';
		} elseif ( $this->unique ) {
			return 'UNIQUE';
		} elseif ( $this->full_text ) {
			return 'FULLTEXT';
		}

		return 'INDEX';
	}

	/**
	 * Make the index unique.
	 *
	 * @param bool $unique Whether the index should be unique.
	 * @return self
	 */
	public function unique( bool $unique = true ): self {
		if ( $unique ) {
			$this->primary   = false;
			$this->full_text = false;
		}
		$this->unique = $unique;
		return $this;
	}

	/**
	 * Make the index full-text.
	 *
	 * @param bool $full_text Whether the index should be full-text.
	 * @return self
	 */
	public function full_text( bool $full_text = true ): self {
		if ( $full_text ) {
			$this->primary = false;
			$this->unique  = false;
		}
		$this->full_text = $full_text;
		return $this;
	}

	/**
	 * Make the index a primary key.
	 *
	 * @param bool $primary Whether the index should be a primary key.
	 * @return self
	 */
	public function primary( bool $primary = true ): self {
		if ($primary) {
			$this->unique    = false;
			$this->full_text = false;
		}
		$this->primary = $primary;
		return $this;
	}
}
