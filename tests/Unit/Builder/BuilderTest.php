<?php

use Sematico\Baselibs\Schema\Builder\Builder;
use Sematico\Baselibs\Schema\Schema;
use Sematico\Baselibs\Schema\Validator;

beforeEach(
	function () {
		$this->builder = new Builder();
	}
);

it(
	'creates a table successfully',
	function () {
		// Mock the global $wpdb
		global $wpdb;
		$wpdb         = Mockery::mock( 'wpdb' );
		$wpdb->prefix = 'wp_';
		$wpdb->shouldReceive( 'query' )->andReturn( true );
		$wpdb->shouldReceive( 'get_charset_collate' )->andReturn( 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci' );

		// Now create the schema with the global $wpdb in place
		$this->schema = new Schema( 'test_table' );

		$this->schema->column( 'id' )->int()->auto_increment();
		$this->schema->index( 'id' )->primary();
		$this->schema->column( 'name' )->varchar( 255 );

		$result = $this->builder->create_table( $this->schema );

		expect( $result )->toBeTrue();
	}
);

it(
	'drops a table successfully',
	function () {
		global $wpdb;
		$wpdb         = Mockery::mock( 'wpdb' );
		$wpdb->prefix = 'wp_';
		$wpdb->shouldReceive( 'query' )->andReturn( true );

		$schema  = new Schema( 'test_table' );
		$builder = new Builder();

		$result = $builder->drop_table( $schema );

		expect( $result )->toBeTrue();
	}
);

it(
	'truncates a table successfully',
	function () {
		global $wpdb;
		$wpdb         = Mockery::mock( 'wpdb' );
		$wpdb->prefix = 'wp_';
		$wpdb->shouldReceive( 'prepare' )->once()->with( 'TRUNCATE TABLE %s;', 'wp_test_table' )->andReturn( 'TRUNCATE TABLE wp_test_table;' );
		$wpdb->shouldReceive( 'query' )->once()->with( 'TRUNCATE TABLE wp_test_table;' )->andReturn( true );

		$builder = new Builder();

		$result = $builder->truncate_table( 'test_table' );

		expect( $result )->toBeTrue();
	}
);

it(
	'fails to create a table when validation fails',
	function () {
		$schema = Mockery::mock( Schema::class );
		$schema->shouldReceive( 'get_columns' )->andReturn( [] );
		$schema->shouldReceive( 'get_indexes' )->andReturn( [] );
		$schema->shouldReceive( 'get_foreign_keys' )->andReturn( [] );
		$schema->shouldReceive( 'get_table_name' )->andReturn( 'wp_test_table' );

		$wpdb = Mockery::mock( 'wpdb' );
		$wpdb->shouldReceive( 'get_charset_collate' )->andReturn( 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci' );
		$wpdb->shouldReceive( 'query' )->andReturn( false );

		$schema->shouldReceive( 'get_wpdb' )->andReturn( $wpdb );

		$builder = new Builder();

		$result = $builder->create_table( $schema );

		expect( $result )->toBeFalse();
	}
);

it(
	'creates a table with complex schema successfully',
	function () {
		global $wpdb;
		$wpdb         = Mockery::mock( 'wpdb' );
		$wpdb->prefix = 'wp_';
		$wpdb->shouldReceive( 'query' )->andReturn( true );
		$wpdb->shouldReceive( 'get_charset_collate' )->andReturn( 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci' );

		$schema = new Schema( 'complex_table' );
		$schema->column( 'id' )->int()->auto_increment();
		$schema->column( 'name' )->varchar( 255 )->nullable( false );
		$schema->column( 'email' )->varchar( 255 );
		$schema->column( 'created_at' )->datetime();
		$schema->index( 'id' )->primary();
		$schema->index( 'email' )->unique();

		$builder = new Builder();

		$result = $builder->create_table( $schema );

		expect( $result )->toBeTrue();
	}
);

it(
	'handles foreign key constraints correctly',
	function () {
		global $wpdb;
		$wpdb         = Mockery::mock( 'wpdb' );
		$wpdb->prefix = 'wp_';
		$wpdb->shouldReceive( 'query' )->andReturn( true );
		$wpdb->shouldReceive( 'get_charset_collate' )->andReturn( 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci' );

		$schema = new Schema( 'posts' );
		$schema->column( 'id' )->int()->auto_increment();
		$schema->column( 'user_id' )->int();
		$schema->column( 'title' )->varchar( 255 );
		$schema->index( 'id' )->primary();
		$schema->foreign_key( 'user_id' )->reference( 'users', 'id' )->on_delete( 'CASCADE' );

		$builder = new Builder();

		$result = $builder->create_table( $schema );

		expect( $result )->toBeTrue();
	}
);

it(
	'fails to create a table when wpdb query fails',
	function () {
		global $wpdb;
		$wpdb         = Mockery::mock( 'wpdb' );
		$wpdb->prefix = 'wp_';
		$wpdb->shouldReceive( 'query' )->andReturn( false );
		$wpdb->shouldReceive( 'get_charset_collate' )->andReturn( 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci' );

		$schema = new Schema( 'test_table' );
		$schema->column( 'id' )->int()->auto_increment();
		$schema->index( 'id' )->primary();

		$builder = new Builder();

		$result = $builder->create_table( $schema );

		expect( $result )->toBeFalse();
	}
);
