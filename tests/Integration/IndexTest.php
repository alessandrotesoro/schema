<?php

use Sematico\Baselibs\Schema\Schema;
use Sematico\Baselibs\Schema\Builder\Builder;

beforeEach(
	function () {
		global $wpdb;
		$this->wpdb       = $wpdb;
		$this->table_name = 'test_indexes';
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
	'creates a primary key index correctly',
	function () {
		$this->schema->column( 'id' )->int()->unsigned()->auto_increment();
		$this->schema->index( 'id' )->primary();
		$this->builder->create_table( $this->schema );

		$indexes = $this->getTableIndexes();

		expect( $indexes )->toHaveKey( 'PRIMARY' );
		expect( $indexes['PRIMARY'] )->toEqual(
			[
				'Column_name' => 'id',
				'Key_name'    => 'PRIMARY',
				'Non_unique'  => '0',
				'Index_type'  => 'BTREE',
			]
		);
	}
);

it(
	'creates a unique index correctly',
	function () {
		$this->schema->column( 'email' )->varchar( 255 );
		$this->schema->index( 'email' )->unique();
		$this->builder->create_table( $this->schema );

		$indexes = $this->getTableIndexes();

		expect( $indexes )->toHaveKey( 'idx_email' );
		expect( $indexes['idx_email'] )->toEqual(
			[
				'Column_name' => 'email',
				'Key_name'    => 'idx_email',
				'Non_unique'  => '0',
				'Index_type'  => 'BTREE',
			]
		);
	}
);

it(
	'creates a regular index correctly',
	function () {
		$this->schema->column( 'username' )->varchar( 100 );
		$this->schema->index( 'username' );
		$this->builder->create_table( $this->schema );

		$indexes = $this->getTableIndexes();

		expect( $indexes )->toHaveKey( 'idx_username' );
		expect( $indexes['idx_username'] )->toEqual(
			[
				'Column_name' => 'username',
				'Key_name'    => 'idx_username',
				'Non_unique'  => '1',
				'Index_type'  => 'BTREE',
			]
		);
	}
);

it(
	'creates a fulltext index correctly',
	function () {
		$this->schema->column( 'content' )->text();
		$this->schema->index( 'content' )->full_text();
		$this->builder->create_table( $this->schema );

		$indexes = $this->getTableIndexes();

		expect( $indexes )->toHaveKey( 'idx_content' );
		expect( $indexes['idx_content'] )->toEqual(
			[
				'Column_name' => 'content',
				'Key_name'    => 'idx_content',
				'Non_unique'  => '1',
				'Index_type'  => 'FULLTEXT',
			]
		);
	}
);

it(
	'creates an index with a custom name correctly',
	function () {
		$this->schema->column( 'status' )->varchar( 20 );
		$this->schema->index( 'status', 'custom_status_index' );
		$this->builder->create_table( $this->schema );

		$indexes = $this->getTableIndexes();

		expect( $indexes )->toHaveKey( 'custom_status_index' );
		expect( $indexes['custom_status_index'] )->toEqual(
			[
				'Column_name' => 'status',
				'Key_name'    => 'custom_status_index',
				'Non_unique'  => '1',
				'Index_type'  => 'BTREE',
			]
		);
	}
);
