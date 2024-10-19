<?php

use Sematico\Baselibs\Schema\Schema;
use Sematico\Baselibs\Schema\Column;
use Sematico\Baselibs\Schema\Index;
use Sematico\Baselibs\Schema\Foreign_Key;
use Sematico\Baselibs\Schema\Exceptions\ColumnDoesNotExistException;

beforeEach(
	function () {
		global $wpdb;
		$wpdb         = Mockery::mock( 'wpdb' );
		$wpdb->prefix = 'wp_';
		$this->schema = new Schema( 'test_table' );
	}
);

it(
	'can get the WordPress database object',
	function () {
		global $wpdb;
		expect( $this->schema->get_wpdb() )->toBe( $wpdb );
	}
);

it(
	'can get the full table name with prefix',
	function () {
		expect( $this->schema->get_table_name() )->toBe( 'wp_test_table' );
	}
);

it(
	'can get the table prefix',
	function () {
		expect( $this->schema->get_table_prefix() )->toBe( 'wp_' );
	}
);

it(
	'can get the table name without prefix',
	function () {
		expect( $this->schema->get_table_name_without_prefix() )->toBe( 'test_table' );
	}
);

it(
	'can define and get columns',
	function () {
		$this->schema->column( 'id' );
		expect( $this->schema->get_columns() )->toHaveKey( 'id' );
	}
);

it(
	'can get a column by its name',
	function () {
		$this->schema->column( 'id' );
		expect( $this->schema->get_column( 'id' ) )->toBeInstanceOf( Column::class );
	}
);

it(
	'throws an exception when getting a non-existent column',
	function () {
		$this->schema->get_column( 'non_existent' );
	}
)->throws( ColumnDoesNotExistException::class );

it(
	'can define and get indexes',
	function () {
		$this->schema->index( 'id' );
		$indexes = $this->schema->get_indexes();
		expect( $indexes )->toHaveCount( 1 );
		expect( $indexes[0]->get_column() )->toBe( 'id' );
	}
);

it(
	'can check if a column exists',
	function () {
		$this->schema->column( 'id' );
		expect( $this->schema->has_column( 'id' ) )->toBeTrue();
		expect( $this->schema->has_column( 'non_existent' ) )->toBeFalse();
	}
);

it(
	'can check if an index exists',
	function () {
		$this->schema->index( 'id' );
		expect( $this->schema->has_index( 'id' ) )->toBeTrue();
		expect( $this->schema->has_index( 'non_existent' ) )->toBeFalse();
	}
);

it(
	'can remove a column',
	function () {
		$this->schema->column( 'id' );
		$this->schema->remove_column( 'id' );
		expect( $this->schema->has_column( 'id' ) )->toBeFalse();
	}
);

it(
	'throws an exception when removing a non-existent column',
	function () {
		$this->schema->remove_column( 'non_existent' );
	}
)->throws( ColumnDoesNotExistException::class );

it(
	'can remove an index',
	function () {
		$this->schema->index( 'id' );
		$this->schema->remove_index( 'id' );
		expect( $this->schema->has_index( 'id' ) )->toBeFalse();
	}
);

it(
	'can define and get foreign keys',
	function () {
		$this->schema->foreign_key( 'user_id' );
		expect( $this->schema->get_foreign_keys() )->toHaveCount( 1 );
	}
);

it(
	'can check if there are foreign keys defined',
	function () {
		$this->schema->foreign_key( 'user_id' );
		expect( $this->schema->has_foreign_keys() )->toBeTrue();
		$this->schema->foreign_key( 'post_id' );
		expect( $this->schema->has_foreign_keys() )->toBeTrue();
	}
);
