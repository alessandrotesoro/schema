<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Column definition class.
 *
 * @package   Sematico\Baselibs\Schema
 * @author    Alessandro Tesoro <alessandro.tesoro@icloud.com>
 * @copyright 2024 Sematico
 * @license   GPL-3.0-or-later
 */

namespace Sematico\Baselibs\Schema;

use Sematico\Baselibs\Schema\Exceptions\InvalidDefaultValueException;

/**
 * Class Column
 *
 * Represents a column in a database table.
 */
class Column {

	/**
	 * Column name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Column type.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Column length.
	 *
	 * @var int|null
	 */
	protected $length;

	/**
	 * Column nullable.
	 *
	 * @var bool
	 */
	protected $nullable = false;

	/**
	 * Column default value.
	 *
	 * @var mixed
	 */
	protected $default;

	/**
	 * Indicates if the default value is a raw SQL expression.
	 *
	 * @var bool
	 */
	protected $default_is_expression = false;

	/**
	 * Column auto increment.
	 *
	 * @var bool
	 */
	protected $auto_increment = false;

	/**
	 * Column precision.
	 *
	 * @var int|null
	 */
	protected $precision;

	/**
	 * Column unsigned.
	 *
	 * @var bool
	 */
	protected $unsigned = false;

	/**
	 * Column scale.
	 *
	 * @var int|null
	 */
	protected $scale;

	/**
	 * Constructor.
	 *
	 * @param string $name Column name.
	 */
	public function __construct( string $name ) {
		$this->name = $name;
	}

	/**
	 * Get the column name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Get the column type.
	 *
	 * @return string|null
	 */
	public function get_type(): ?string {
		return $this->type;
	}

	/**
	 * Get the column length.
	 *
	 * @return int|null
	 */
	public function get_length(): ?int {
		return $this->length;
	}

	/**
	 * Check if the column is nullable.
	 *
	 * @return bool
	 */
	public function is_nullable(): bool {
		return $this->nullable;
	}

	/**
	 * Get the default value of the column.
	 *
	 * @return mixed
	 */
	public function get_default() {
		return $this->default;
	}

	/**
	 * Check if the column is auto-incrementing.
	 *
	 * @return bool
	 */
	public function is_auto_increment(): bool {
		return $this->auto_increment;
	}

	/**
	 * Get the precision of the column.
	 *
	 * @return int|null
	 */
	public function get_precision(): ?int {
		return $this->precision;
	}

	/**
	 * Get the scale of the column.
	 *
	 * @return int|null
	 */
	public function get_scale(): ?int {
		return $this->scale;
	}

	/**
	 * Check if the column is unsigned.
	 *
	 * @return bool
	 */
	public function is_unsigned(): bool {
		return $this->unsigned;
	}

	/**
	 * Set the column type.
	 *
	 * @param string $type The SQL data type for the column.
	 * @return self
	 */
	public function type( string $type ): self {
		$this->type = $type;
		return $this;
	}

	/**
	 * Set the column length.
	 *
	 * @param int $length The maximum length for the column.
	 * @return self
	 */
	public function length( int $length ): self {
		$this->length = $length;
		return $this;
	}

	/**
	 * Set the column as nullable.
	 *
	 * @param bool $nullable Whether the column can be null.
	 * @return self
	 */
	public function nullable( bool $nullable = true ): self {
		$this->nullable = $nullable;
		return $this;
	}

	/**
	 * Set the default value for the column.
	 *
	 * @param mixed $value The default value.
	 * @param bool  $is_expression Whether the value is a raw SQL expression.
	 * @return self
	 * @throws InvalidDefaultValueException If an invalid default value is provided.
	 */
	public function default( $value, bool $is_expression = false ): self {
		if ( $is_expression && ! is_string( $value ) ) {
			throw new InvalidDefaultValueException( 'SQL expression must be a string.' );
		}

		$this->default               = $value;
		$this->default_is_expression = $is_expression;
		return $this;
	}

	/**
	 * Check if the default value is a raw SQL expression.
	 *
	 * @return bool
	 */
	public function is_default_expression(): bool {
		return $this->default_is_expression;
	}

	/**
	 * Set the column as auto-incrementing.
	 *
	 * @param bool $auto_increment Whether the column should auto-increment.
	 * @return self
	 */
	public function auto_increment( bool $auto_increment = true ): self {
		$this->auto_increment = $auto_increment;
		return $this;
	}

	/**
	 * Set the precision for numeric columns.
	 *
	 * @param int $precision The precision (total digits) for numeric columns.
	 * @return self
	 */
	public function precision( int $precision ): self {
		$this->precision = $precision;
		return $this;
	}

	/**
	 * Set the column as unsigned (for numeric types).
	 *
	 * @param bool $unsigned Whether the column should be unsigned.
	 * @return self
	 */
	public function unsigned( bool $unsigned = true ): self {
		$this->unsigned = $unsigned;
		return $this;
	}

	/**
	 * Set the column type to VARCHAR.
	 *
	 * @param int $length The maximum length for the VARCHAR column.
	 * @return self
	 */
	public function varchar( int $length = 255 ): self {
		$this->type   = 'VARCHAR';
		$this->length = $length;
		return $this;
	}

	/**
	 * Set the column type to INT.
	 *
	 * @param bool $unsigned Whether the INT should be unsigned.
	 * @return self
	 */
	public function int( bool $unsigned = false ): self {
		$this->type     = 'INT';
		$this->unsigned = $unsigned;
		return $this;
	}

	/**
	 * Set the column type to TEXT.
	 *
	 * @return self
	 */
	public function text(): self {
		$this->type = 'TEXT';
		return $this;
	}

	/**
	 * Set the column type to DATETIME.
	 *
	 * @return self
	 */
	public function datetime(): self {
		$this->type = 'DATETIME';
		return $this;
	}

	/**
	 * Set the column type to BOOLEAN.
	 *
	 * @return self
	 */
	public function boolean(): self {
		$this->type = 'BOOLEAN';
		return $this;
	}

	/**
	 * Set the column type to TINYINT.
	 *
	 * @param bool $unsigned Whether the TINYINT should be unsigned.
	 * @return self
	 */
	public function tinyint( bool $unsigned = false ): self {
		$this->type     = 'TINYINT';
		$this->unsigned = $unsigned;
		return $this;
	}

	/**
	 * Set the column type to BIGINT.
	 *
	 * @param bool $unsigned Whether the BIGINT should be unsigned.
	 * @return self
	 */
	public function bigint( bool $unsigned = false ): self {
		$this->type     = 'BIGINT';
		$this->unsigned = $unsigned;
		return $this;
	}

	/**
	 * Set the column type to FLOAT with specified precision and scale.
	 *
	 * @param int|null $precision The total number of digits (null for system default).
	 * @param int|null $scale The number of digits after the decimal point (null for system default).
	 * @return self
	 */
	public function float(?int $precision = null, ?int $scale = null): self {
		$this->type      = 'FLOAT';
		$this->precision = $precision;
		$this->scale     = $scale;
		return $this;
	}

	/**
	 * Set the column type to DECIMAL with specified precision and scale.
	 *
	 * @param int $precision The total number of digits.
	 * @param int $scale The number of digits after the decimal point.
	 * @return self
	 */
	public function decimal( int $precision, int $scale ): self {
		$this->type      = 'DECIMAL';
		$this->precision = $precision;
		$this->scale     = $scale;
		return $this;
	}

	/**
	 * Set the column type to DATE.
	 *
	 * @return self
	 */
	public function date(): self {
		$this->type = 'DATE';
		return $this;
	}

	/**
	 * Set the column type to TIME.
	 *
	 * @return self
	 */
	public function time(): self {
		$this->type = 'TIME';
		return $this;
	}

	/**
	 * Set the column type to TIMESTAMP.
	 *
	 * @return self
	 */
	public function timestamp(): self {
		$this->type = 'TIMESTAMP';
		return $this;
	}
}
