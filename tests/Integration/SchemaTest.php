<?php

use Sematico\Baselibs\Schema\Builder\Builder;
use Sematico\Baselibs\Schema\Schema;

beforeEach(
	function () {
		global $wpdb;
		$table_name = $wpdb->prefix . 'schema_test';
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	}
);

it(
	'can create a table',
	function () {
		$table = new Schema(
			'schema_test',
			function ( Schema $table ) {
				$table->column( 'id' )
					->int()
					->unsigned()
					->auto_increment();

				$table->column( 'name' )
					->varchar( 255 );

				$table->column( 'age' )
					->int()
					->unsigned();

				$table->index( 'id' )->primary();
			}
		);

		$builder = new Builder();
		$builder->create_table( $table );

		expect( $table )->toBeInstanceOf( Schema::class );
		expect( $builder )->toBeInstanceOf( Builder::class );

		// Check if the table exists.
		global $wpdb;
		$table_name   = $wpdb->prefix . 'schema_test';
		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

		expect( $table_exists )->toBe( $table_name );
	}
);

it(
	'can drop a table',
	function () {
		$table = new Schema(
			'schema_test',
			function ( Schema $table ) {
				$table->column( 'id' )->int()->unsigned()->auto_increment();

				$table->index( 'id' )->primary();
			}
		);

		$builder = new Builder();
		$builder->create_table( $table );

		$builder->drop_table( $table );

		global $wpdb;
		$table_name   = $wpdb->prefix . 'schema_test';
		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );
		expect( $table_exists )->toBe( null );
	}
);
