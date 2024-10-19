<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Grammar class.
 *
 * @package   Sematico\Baselibs\Schema
 * @author    Alessandro Tesoro <alessandro.tesoro@icloud.com>
 * @copyright 2024 Sematico
 * @license   GPL-3.0-or-later
 */

namespace Sematico\Baselibs\Schema\Grammar;

use Sematico\Baselibs\Schema\Schema;
use Sematico\Baselibs\Schema\Column;
use Sematico\Baselibs\Schema\Index;
use Sematico\Baselibs\Schema\Foreign_Key;

/**
 * This class is responsible for compiling schema definitions into MySQL create table statements.
 */
class Grammar implements GrammarInterface {

	/**
	 * Compile the schema into a MySQL create table statement.
	 *
	 * @param Schema $schema The schema object.
	 * @return string The MySQL create table statement.
	 */
	public function compile( Schema $schema ): string {
		$table_name   = $schema->get_table_name();
		$columns      = $this->compile_columns( $schema->get_columns() );
		$indexes      = $this->compile_indexes( $schema->get_indexes() );
		$foreign_keys = $this->compile_foreign_keys( $schema->get_foreign_keys() );
		$charset      = $schema->get_wpdb()->get_charset_collate();

		return sprintf(
			"CREATE TABLE %s (\n%s%s%s\n) %s;",
			$table_name,
			$columns,
			$indexes ? ",\n" . $indexes : '',
			$foreign_keys ? ",\n" . $foreign_keys : '',
			$charset
		);
	}

	/**
	 * Compile the columns into a MySQL column definition string.
	 *
	 * @param Column[] $columns The array of column objects.
	 * @return string The MySQL column definition string.
	 */
	protected function compile_columns( array $columns ): string {
		$sql = [];
		foreach ($columns as $column) {
			$sql[] = $this->compile_column( $column );
		}
		return implode( ",\n", $sql );
	}

	/**
	 * Compile a single column into a MySQL column definition string.
	 *
	 * @param Column $column The column object.
	 * @return string The MySQL column definition string.
	 */
	protected function compile_column( Column $column ): string {
		$sql = sprintf(
			'%s %s%s%s%s%s',
			$column->get_name(),
			$column->get_type(),
			$this->get_column_length( $column ),
			$column->is_unsigned() ? ' UNSIGNED' : '',
			$column->is_nullable() ? ' NULL' : ' NOT NULL',
			$column->is_auto_increment() ? ' AUTO_INCREMENT' : ''
		);

		if ( $column->get_default() !== null ) {
			if ( $column->is_default_expression() ) {
				$sql .= " DEFAULT {$column->get_default()}";
			} else {
				$sql .= " DEFAULT '{$column->get_default()}'";
			}
		}

		return $sql;
	}

	/**
	 * Get the column length or precision/scale for decimal and float columns.
	 *
	 * @param Column $column The column object.
	 * @return string The formatted length string or an empty string if not applicable.
	 */
	protected function get_column_length( Column $column ): string {
		$type      = $column->get_type();
		$precision = $column->get_precision();
		$scale     = $column->get_scale();

		if (( $type === 'DECIMAL' || $type === 'FLOAT' ) && $precision !== null) {
			if ($scale !== null) {
				return "($precision,$scale)";
			}
			return "($precision)";
		}

		return $column->get_length() ? "({$column->get_length()})" : '';
	}

	/**
	 * Compile the indexes into a MySQL index definition string.
	 *
	 * @param Index[] $indexes The array of index objects.
	 * @return string The MySQL index definition string.
	 */
	protected function compile_indexes( array $indexes ): string {
		$sql = [];
		foreach ($indexes as $index) {
			$sql[] = $this->compile_index( $index );
		}
		return implode( ",\n", $sql );
	}

	/**
	 * Compile a single index into a MySQL index definition string.
	 *
	 * @param Index $index The index object.
	 * @return string The MySQL index definition string.
	 */
	protected function compile_index( Index $index ): string {
		$type   = $index->get_type();
		$name   = $index->get_name();
		$column = $index->get_column();

		if ($type === 'PRIMARY KEY') {
			return "PRIMARY KEY (`$column`)";
		}

		return sprintf(
			'%s `%s` (`%s`)',
			$type,
			$name,
			$column
		);
	}

	/**
	 * Compile the foreign keys into a MySQL foreign key definition string.
	 *
	 * @param Foreign_Key[] $foreign_keys The array of foreign key objects.
	 * @return string The MySQL foreign key definition string.
	 */
	protected function compile_foreign_keys( array $foreign_keys ): string {
		$sql = [];
		foreach ( $foreign_keys as $foreign_key ) {
			$sql[] = $this->compile_foreign_key( $foreign_key );
		}
		return implode( ",\n", $sql );
	}

	/**
	 * Compile a single foreign key into a MySQL foreign key definition string.
	 *
	 * @param Foreign_Key $foreign_key The foreign key object.
	 * @return string The MySQL foreign key definition string.
	 */
	protected function compile_foreign_key( Foreign_Key $foreign_key ): string {
		return sprintf(
			'CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `%s` (`%s`) ON DELETE %s ON UPDATE %s',
			$foreign_key->get_name(),
			$foreign_key->get_column(),
			$foreign_key->get_reference_table(),
			$foreign_key->get_reference_column(),
			$foreign_key->get_on_delete(),
			$foreign_key->get_on_update()
		);
	}
}
