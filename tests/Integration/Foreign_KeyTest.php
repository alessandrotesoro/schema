<?php

use Sematico\Baselibs\Schema\Schema;
use Sematico\Baselibs\Schema\Builder\Builder;

beforeEach(
	function () {
		global $wpdb;
		$this->wpdb    = $wpdb;
		$this->builder = new Builder();

		// Create a 'users' table
		$this->users_schema = new Schema( 'test_users' );
		$this->users_schema->column( 'id' )->int()->unsigned()->auto_increment();
		$this->users_schema->index( 'id' )->primary();
		$this->users_schema->column( 'name' )->varchar( 100 );
		$this->builder->create_table( $this->users_schema );

		// Create a 'posts' table with a foreign key to 'users'
		$this->posts_schema = new Schema( 'test_posts' );
		$this->posts_schema->column( 'id' )->int()->unsigned()->auto_increment();
		$this->posts_schema->index( 'id' )->primary();
		$this->posts_schema->column( 'user_id' )->int()->unsigned();
		$this->posts_schema->column( 'title' )->varchar( 255 );
	}
);

afterEach(
	function () {
		$this->builder->drop_table( $this->posts_schema );
		$this->builder->drop_table( $this->users_schema );
	}
);

it(
	'creates a foreign key correctly',
	function () {
		$this->posts_schema->foreign_key( 'user_id' )
		->reference( $this->users_schema->get_table_name(), 'id' )
		->on_delete( 'CASCADE' )
		->on_update( 'CASCADE' );

		$this->builder->create_table( $this->posts_schema );

		$foreign_keys = $this->getForeignKeys( $this->posts_schema->get_table_name() );

		expect( $foreign_keys )->toHaveCount( 1 );
		expect( $foreign_keys[0]->COLUMN_NAME )->toBe( 'user_id' );
		expect( $foreign_keys[0]->REFERENCED_TABLE_NAME )->toBe( $this->users_schema->get_table_name() );
		expect( $foreign_keys[0]->REFERENCED_COLUMN_NAME )->toBe( 'id' );
	}
);

it(
	'creates a foreign key with custom name',
	function () {
		$custom_fk_name = 'fk_user_posts';
		$this->posts_schema->foreign_key( 'user_id', $custom_fk_name )
		->reference( $this->users_schema->get_table_name(), 'id' );

		$this->builder->create_table( $this->posts_schema );

		$foreign_keys = $this->getForeignKeys( $this->posts_schema->get_table_name() );

		expect( $foreign_keys )->toHaveCount( 1 );
		expect( $foreign_keys[0]->CONSTRAINT_NAME )->toBe( $custom_fk_name );
	}
);

it(
	'creates a foreign key with SET NULL on delete',
	function () {
		$this->posts_schema->column( 'user_id' )->int()->unsigned()->nullable();
		$this->posts_schema->foreign_key( 'user_id' )
		->reference( $this->users_schema->get_table_name(), 'id' )
		->on_delete( 'SET NULL' )
		->on_update( 'CASCADE' );

		$this->builder->create_table( $this->posts_schema );

		$foreign_keys = $this->getForeignKeys( $this->posts_schema->get_table_name() );

		expect( $foreign_keys )->toHaveCount( 1 );

		// Check if DELETE_RULE exists
		expect( isset( $foreign_keys[0]->DELETE_RULE ) )->toBeTrue( 'DELETE_RULE is not set' );

		if ( isset( $foreign_keys[0]->DELETE_RULE ) ) {
			expect( $foreign_keys[0]->DELETE_RULE )->toBe( 'SET NULL' );
		}

		// Check if UPDATE_RULE exists
		expect( isset( $foreign_keys[0]->UPDATE_RULE ) )->toBeTrue( 'UPDATE_RULE is not set' );

		if ( isset( $foreign_keys[0]->UPDATE_RULE ) ) {
			expect( $foreign_keys[0]->UPDATE_RULE )->toBe( 'CASCADE' );
		}
	}
);

it(
	'creates a foreign key with RESTRICT on update',
	function () {
		$this->posts_schema->foreign_key( 'user_id' )
		->reference( $this->users_schema->get_table_name(), 'id' )
		->on_delete( 'CASCADE' )
		->on_update( 'RESTRICT' );

		$this->builder->create_table( $this->posts_schema );

		$foreign_keys = $this->getForeignKeys( $this->posts_schema->get_table_name() );

		expect( $foreign_keys )->toHaveCount( 1 );
		expect( $foreign_keys[0]->DELETE_RULE )->toBe( 'CASCADE' );
		expect( $foreign_keys[0]->UPDATE_RULE )->toBe( 'RESTRICT' );
	}
);

it(
	'creates multiple foreign keys',
	function () {
		// Create a 'categories' table
		$categories_schema = new Schema( 'test_categories' );
		$categories_schema->column( 'id' )->int()->unsigned()->auto_increment();
		$categories_schema->index( 'id' )->primary();
		$categories_schema->column( 'name' )->varchar( 100 );

		// Check if the table exists before creating it
		if ( ! $this->tableExists( $categories_schema->get_table_name() )) {
			$this->builder->create_table( $categories_schema );
		}

		// Add category_id to posts table
		$this->posts_schema->column( 'category_id' )->int()->unsigned();

		// Create foreign keys
		$this->posts_schema->foreign_key( 'user_id' )->reference( $this->users_schema->get_table_name(), 'id' );
		$this->posts_schema->foreign_key( 'category_id' )->reference( $categories_schema->get_table_name(), 'id' );

		$this->builder->create_table( $this->posts_schema );

		$foreign_keys = $this->getForeignKeys( $this->posts_schema->get_table_name() );

		expect( $foreign_keys )->toHaveCount( 2 );
		expect( $foreign_keys[0]->COLUMN_NAME )->toBe( 'user_id' );
		expect( $foreign_keys[1]->COLUMN_NAME )->toBe( 'category_id' );

		// Clean up
		$this->builder->drop_table( $this->posts_schema );
		$this->builder->drop_table( $categories_schema );
	}
);

it(
	'fails to create a foreign key with non-existent reference',
	function () {
		// Temporarily disable WordPress database errors
		$this->wpdb->hide_errors();

		// Store the original error_reporting level
		$original_error_reporting = error_reporting();

		// Disable error reporting for this test
		error_reporting( 0 );

		$this->posts_schema->foreign_key( 'user_id' )
			->reference( 'non_existent_table', 'id' );

		$result = $this->builder->create_table( $this->posts_schema );

		expect( $result )->toBeFalse();

		// Restore the original error_reporting level
		error_reporting( $original_error_reporting );

		// Restore the original error_reporting level
		error_reporting( $original_error_reporting );

		// Resotre wpdb error reporting
		$this->wpdb->show_errors();
	}
);
