<?php

use Sematico\Baselibs\Schema\Validator;
use Sematico\Baselibs\Schema\Schema;
use Sematico\Baselibs\Schema\Column;
use Sematico\Baselibs\Schema\Index;

beforeEach(
	function () {
		$this->schema    = Mockery::mock( Schema::class );
		$this->validator = new Validator( $this->schema );
	}
);

it(
	'validates columns successfully',
	function () {
		$columns = [
			Mockery::mock(
				Column::class,
				[
					'get_name' => 'id',
					'get_type' => 'int',
				]
			),
			Mockery::mock(
				Column::class,
				[
					'get_name' => 'name',
					'get_type' => 'string',
				]
			),
		];

		$this->schema->shouldReceive( 'get_columns' )->andReturn( $columns );

		expect( $this->validator->validate_columns() )->toBeTrue();
	}
);

it(
	'throws exception for duplicate column names',
	function () {
		$columns = [
			Mockery::mock(
				Column::class,
				[
					'get_name' => 'id',
					'get_type' => 'int',
				]
			),
			Mockery::mock(
				Column::class,
				[
					'get_name' => 'id',
					'get_type' => 'string',
				]
			),
		];

		$this->schema->shouldReceive( 'get_columns' )->andReturn( $columns );

		$this->validator->validate_columns();
	}
)->throws( Exception::class, 'Duplicate column name found: id' );

it(
	'throws exception for undefined column type',
	function () {
		$columns = [
			Mockery::mock(
				Column::class,
				[
					'get_name' => 'id',
					'get_type' => '',
				]
			),
		];

		$this->schema->shouldReceive( 'get_columns' )->andReturn( $columns );

		$this->validator->validate_columns();
	}
)->throws( Exception::class, 'Column type not defined for column: id' );

it(
	'validates indexes successfully',
	function () {
		$indexes = [
			Mockery::mock(
				Index::class,
				[
					'get_name'   => 'primary',
					'get_column' => 'id',
				]
			),
		];

		$this->schema->shouldReceive( 'get_indexes' )->andReturn( $indexes );

		expect( $this->validator->validate_indexes() )->toBeTrue();
	}
);

it(
	'throws exception for index without column',
	function () {
		$indexes = [
			Mockery::mock(
				Index::class,
				[
					'get_name'   => 'primary',
					'get_column' => '',
				]
			),
		];

		$this->schema->shouldReceive( 'get_indexes' )->andReturn( $indexes );

		$this->validator->validate_indexes();
	}
)->throws( Exception::class, 'Index column not defined for index: primary' );

it(
	'validates indexes have columns',
	function () {
		$indexes = [
			Mockery::mock(
				Index::class,
				[
					'get_name'   => 'primary',
					'get_column' => 'id',
				]
			),
		];

		$this->schema->shouldReceive( 'get_indexes' )->andReturn( $indexes );
		$this->schema->shouldReceive( 'has_column' )->with( 'id' )->andReturn( true );

		expect( $this->validator->validate_indexes_have_column() )->toBeTrue();
	}
);

it(
	'throws exception for index without valid column',
	function () {
		$indexes = [
			Mockery::mock(
				Index::class,
				[
					'get_name'   => 'primary',
					'get_column' => 'id',
				]
			),
		];

		$this->schema->shouldReceive( 'get_indexes' )->andReturn( $indexes );
		$this->schema->shouldReceive( 'has_column' )->with( 'id' )->andReturn( false );

		$this->validator->validate_indexes_have_column();
	}
)->throws( Exception::class, 'Index does not have a valid column defined: primary' );

it(
	'validates single primary key',
	function () {
		$indexes = [
			Mockery::mock(
				Index::class,
				[
					'get_name'   => 'primary',
					'is_primary' => true,
				]
			),
		];

		$this->schema->shouldReceive( 'get_indexes' )->andReturn( $indexes );

		expect( $this->validator->validate_primary_key() )->toBeTrue();
	}
);

it(
	'throws exception for multiple primary keys',
	function () {
		$indexes = [
			Mockery::mock(
				Index::class,
				[
					'get_name'   => 'primary1',
					'is_primary' => true,
				]
			),
			Mockery::mock(
				Index::class,
				[
					'get_name'   => 'primary2',
					'is_primary' => true,
				]
			),
		];

		$this->schema->shouldReceive( 'get_indexes' )->andReturn( $indexes );

		$this->validator->validate_primary_key();
	}
)->throws( Exception::class, 'Multiple primary keys defined.' );

it(
	'validates primary key nullability',
	function () {
		$indexes = [
			Mockery::mock(
				Index::class,
				[
					'get_name'   => 'primary',
					'is_primary' => true,
					'get_column' => 'id',
				]
			),
		];
		$column  = Mockery::mock(
			Column::class,
			[
				'get_name'    => 'id',
				'is_nullable' => false,
			]
		);

		$this->schema->shouldReceive( 'get_indexes' )->andReturn( $indexes );
		$this->schema->shouldReceive( 'get_column' )->with( 'id' )->andReturn( $column );

		expect( $this->validator->validate_primary_key_nullability() )->toBeTrue();
	}
);

it(
	'throws exception for nullable primary key column',
	function () {
		$indexes = [
			Mockery::mock(
				Index::class,
				[
					'get_name'   => 'primary',
					'is_primary' => true,
					'get_column' => 'id',
				]
			),
		];
		$column  = Mockery::mock(
			Column::class,
			[
				'get_name'    => 'id',
				'is_nullable' => true,
			]
		);

		$this->schema->shouldReceive( 'get_indexes' )->andReturn( $indexes );
		$this->schema->shouldReceive( 'get_column' )->with( 'id' )->andReturn( $column );

		$this->validator->validate_primary_key_nullability();
	}
)->throws( Exception::class, 'Primary key column cannot be nullable: id' );

it(
	'validates auto-increment primary key',
	function () {
		$columns = [
			Mockery::mock(
				Column::class,
				[
					'get_name'          => 'id',
					'is_auto_increment' => true,
				]
			),
		];
		$indexes = [
			Mockery::mock(
				Index::class,
				[
					'get_name'   => 'primary',
					'is_primary' => true,
					'get_column' => 'id',
				]
			),
		];

		$this->schema->shouldReceive( 'get_columns' )->andReturn( $columns );
		$this->schema->shouldReceive( 'get_indexes' )->andReturn( $indexes );

		expect( $this->validator->validate_auto_increment_primary_key() )->toBeTrue();
	}
);

it(
	'throws exception for auto-increment column not set as primary index',
	function () {
		$columns = [
			Mockery::mock(
				Column::class,
				[
					'get_name'          => 'id',
					'is_auto_increment' => true,
				]
			),
		];
		$indexes = [
			Mockery::mock(
				Index::class,
				[
					'get_name'   => 'primary',
					'is_primary' => false,
					'get_column' => 'id',
				]
			),
		];

		$this->schema->shouldReceive( 'get_columns' )->andReturn( $columns );
		$this->schema->shouldReceive( 'get_indexes' )->andReturn( $indexes );

		$this->validator->validate_auto_increment_primary_key();
	}
)->throws( Exception::class, 'Auto-increment column is not set as primary index: id' );

it(
	'validates the schema successfully',
	function () {
		$columns = [
			Mockery::mock(
				Column::class,
				[
					'get_name'          => 'id',
					'get_type'          => 'int',
					'is_auto_increment' => true,
					'is_nullable'       => false, // Add this line
				]
			),
			Mockery::mock(
				Column::class,
				[
					'get_name' => 'name',
					'get_type' => 'string',
				]
			),
		];
		$indexes = [
			Mockery::mock(
				Index::class,
				[
					'get_name'   => 'primary',
					'is_primary' => true,
					'get_column' => 'id',
				]
			),
		];

		$this->schema->shouldReceive( 'get_columns' )->andReturn( $columns );
		$this->schema->shouldReceive( 'get_indexes' )->andReturn( $indexes );
		$this->schema->shouldReceive( 'has_column' )->with( 'id' )->andReturn( true );
		$this->schema->shouldReceive( 'get_column' )->with( 'id' )->andReturn( $columns[0] ); // Add this line

		expect( $this->validator->validate() )->toBeTrue();
	}
);
