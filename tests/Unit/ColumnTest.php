<?php

use Sematico\Baselibs\Schema\Column;

it(
	'can set and get the column name',
	function () {
		$column = new Column( 'test_column' );
		expect( $column->get_name() )->toBe( 'test_column' );
	}
);

it(
	'can set and get the column type',
	function () {
		$column = new Column( 'test_column' );
		$column->type( 'VARCHAR' );
		expect( $column->get_type() )->toBe( 'VARCHAR' );
	}
);

it(
	'can set and get the column length',
	function () {
		$column = new Column( 'test_column' );
		$column->length( 255 );
		expect( $column->get_length() )->toBe( 255 );
	}
);

it(
	'can set and get the column nullable property',
	function () {
		$column = new Column( 'test_column' );
		$column->nullable( true );
		expect( $column->is_nullable() )->toBeTrue();
	}
);

it(
	'can set and get the column default value',
	function () {
		$column = new Column( 'test_column' );
		$column->default( 'default_value' );
		expect( $column->get_default() )->toBe( 'default_value' );
	}
);

it(
	'can set and get the column auto increment property',
	function () {
		$column = new Column( 'test_column' );
		$column->auto_increment( true );
		expect( $column->is_auto_increment() )->toBeTrue();
	}
);

it(
	'can set and get the column precision',
	function () {
		$column = new Column( 'test_column' );
		$column->precision( 10 );
		expect( $column->get_precision() )->toBe( 10 );
	}
);

it(
	'can set and get the column unsigned property',
	function () {
		$column = new Column( 'test_column' );
		$column->unsigned( true );
		expect( $column->is_unsigned() )->toBeTrue();
	}
);

it(
	'can set the column type to VARCHAR',
	function () {
		$column = new Column( 'test_column' );
		$column->varchar( 255 );
		expect( $column->get_type() )->toBe( 'VARCHAR' );
		expect( $column->get_length() )->toBe( 255 );
	}
);

it(
	'can set the column type to INT',
	function () {
		$column = new Column( 'test_column' );
		$column->int( true );
		expect( $column->get_type() )->toBe( 'INT' );
		expect( $column->is_unsigned() )->toBeTrue();
	}
);

it(
	'can set the column type to TEXT',
	function () {
		$column = new Column( 'test_column' );
		$column->text();
		expect( $column->get_type() )->toBe( 'TEXT' );
	}
);

it(
	'can set the column type to DATETIME',
	function () {
		$column = new Column( 'test_column' );
		$column->datetime();
		expect( $column->get_type() )->toBe( 'DATETIME' );
	}
);

it(
	'can set the column type to BOOLEAN',
	function () {
		$column = new Column( 'test_column' );
		$column->boolean();
		expect( $column->get_type() )->toBe( 'BOOLEAN' );
	}
);

it(
	'can set the column type to TINYINT',
	function () {
		$column = new Column( 'test_column' );
		$column->tinyint( true );
		expect( $column->get_type() )->toBe( 'TINYINT' );
		expect( $column->is_unsigned() )->toBeTrue();
	}
);

it(
	'can set the column type to BIGINT',
	function () {
		$column = new Column( 'test_column' );
		$column->bigint( true );
		expect( $column->get_type() )->toBe( 'BIGINT' );
		expect( $column->is_unsigned() )->toBeTrue();
	}
);

it(
	'can set the column type to FLOAT',
	function () {
		$column = new Column( 'test_column' );
		$column->float( 10, true );
		expect( $column->get_type() )->toBe( 'FLOAT' );
		expect( $column->get_precision() )->toBe( 10 );
		expect( $column->is_unsigned() )->toBeTrue();
	}
);

it(
	'can set the column type to DECIMAL',
	function () {
		$column = new Column( 'test_column' );
		$column->decimal( 10, 2, true );
		expect( $column->get_type() )->toBe( 'DECIMAL' );
		expect( $column->get_precision() )->toBe( 10 );
		expect( $column->get_scale() )->toBe( 2 );
	}
);

it(
	'can set the column type to DATE',
	function () {
		$column = new Column( 'test_column' );
		$column->date();
		expect( $column->get_type() )->toBe( 'DATE' );
	}
);

it(
	'can set the column type to TIME',
	function () {
		$column = new Column( 'test_column' );
		$column->time();
		expect( $column->get_type() )->toBe( 'TIME' );
	}
);

it(
	'can set the column type to TIMESTAMP',
	function () {
		$column = new Column( 'test_column' );
		$column->timestamp();
		expect( $column->get_type() )->toBe( 'TIMESTAMP' );
	}
);
