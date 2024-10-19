<?php

use Sematico\Baselibs\Schema\Schema;
use Sematico\Baselibs\Schema\Builder\Builder;

beforeEach(
	function () {
		global $wpdb;
		$this->wpdb       = $wpdb;
		$this->table_name = 'test_columns';
		$this->schema     = new Schema( $this->table_name );
		$this->builder    = new Builder();
	}
);

afterEach(
	function () {
		$this->builder->drop_table( $this->schema );
	}
);

it(
	'creates an integer column correctly',
	function () {
		$this->schema->column( 'id' )->int()->unsigned()->auto_increment();
		$this->schema->index( 'id' )->primary();
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'id' );

		expect( $column_info->Type )->toBe( 'int unsigned' );
		expect( $column_info->Null )->toBe( 'NO' );
		expect( $column_info->Extra )->toBe( 'auto_increment' );
	}
);

it(
	'creates a varchar column correctly',
	function () {
		$this->schema->column( 'name' )->varchar( 100 )->nullable();
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'name' );

		expect( $column_info->Type )->toBe( 'varchar(100)' );
		expect( $column_info->Null )->toBe( 'YES' );
	}
);

it(
	'creates a datetime column correctly',
	function () {
		$this->schema->column( 'created_at' )->datetime()->default( 'CURRENT_TIMESTAMP', true );
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'created_at' );

		expect( $column_info->Type )->toBe( 'datetime' );
		expect( $column_info->Null )->toBe( 'NO' );
		expect( $column_info->Default )->toBe( 'CURRENT_TIMESTAMP' );
	}
);

it(
	'creates a text column correctly',
	function () {
		$this->schema->column( 'description' )->text();
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'description' );

		ray( $column_info );

		expect( $column_info->Type )->toBe( 'text' );
		expect( $column_info->Null )->toBe( 'NO' );
	}
);

it(
	'creates a boolean column correctly',
	function () {
		$this->schema->column( 'is_active' )->boolean()->default( true );
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'is_active' );

		expect( $column_info->Type )->toBe( 'tinyint(1)' );
		expect( $column_info->Null )->toBe( 'NO' );
		expect( $column_info->Default )->toBe( '1' );
	}
);

it(
	'creates a decimal column correctly',
	function () {
		$this->schema->column( 'price' )->decimal( 8, 2 )->unsigned();
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'price' );

		ray( $column_info );

		expect( $column_info->Type )->toBe( 'decimal(8,2) unsigned' );
		expect( $column_info->Null )->toBe( 'NO' );
	}
);

it(
	'creates a primary key correctly',
	function () {
		$this->schema->column( 'id' )->int()->unsigned()->auto_increment();
		$this->schema->index( 'id' )->primary();
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'id' );

		expect( $column_info->Key )->toBe( 'PRI' );
	}
);

it(
	'creates a unique index correctly',
	function () {
		$this->schema->column( 'id' )->int()->unsigned()->auto_increment();
		$this->schema->index( 'id' )->primary();

		$this->schema->column( 'email' )->varchar( 255 );
		$this->schema->index( 'email' )->unique();
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'email' );

		expect( $column_info->Key )->toBe( 'UNI' );
	}
);

it(
	'creates a float column correctly',
	function () {
		$this->schema->column( 'rating' )->float( 3, 1 );
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'rating' );

		expect( $column_info->Type )->toBe( 'float(3,1)' );
		expect( $column_info->Null )->toBe( 'NO' );
	}
);

it(
	'creates a date column correctly',
	function () {
		$this->schema->column( 'birth_date' )->date();
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'birth_date' );

		expect( $column_info->Type )->toBe( 'date' );
		expect( $column_info->Null )->toBe( 'NO' );
	}
);

it(
	'creates a time column correctly',
	function () {
		$this->schema->column( 'start_time' )->time();
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'start_time' );

		expect( $column_info->Type )->toBe( 'time' );
		expect( $column_info->Null )->toBe( 'NO' );
	}
);

it(
	'creates a timestamp column correctly',
	function () {
		$this->schema->column( 'last_login' )->timestamp()->default( 'CURRENT_TIMESTAMP', true );
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'last_login' );

		expect( $column_info->Type )->toBe( 'timestamp' );
		expect( $column_info->Null )->toBe( 'NO' );
		expect( $column_info->Default )->toBe( 'CURRENT_TIMESTAMP' );
	}
);

it(
	'creates a bigint column correctly',
	function () {
		$this->schema->column( 'big_number' )->bigint()->unsigned();
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'big_number' );

		expect( $column_info->Type )->toBe( 'bigint unsigned' );
		expect( $column_info->Null )->toBe( 'NO' );
	}
);

it(
	'creates a tinyint column correctly',
	function () {
		$this->schema->column( 'small_number' )->tinyint();
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'small_number' );

		expect( $column_info->Type )->toBe( 'tinyint' );
		expect( $column_info->Null )->toBe( 'NO' );
	}
);

it(
	'creates a column with a default value correctly',
	function () {
		$this->schema->column( 'status' )->varchar( 20 )->default( 'pending' );
		$this->builder->create_table( $this->schema );

		$column_info = $this->getColumnInfo( 'status' );

		expect( $column_info->Type )->toBe( 'varchar(20)' );
		expect( $column_info->Null )->toBe( 'NO' );
		expect( $column_info->Default )->toBe( 'pending' );
	}
);
