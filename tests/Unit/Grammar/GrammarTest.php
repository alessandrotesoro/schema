<?php

use Sematico\Baselibs\Schema\Grammar\Grammar;
use Sematico\Baselibs\Schema\Schema;
use Sematico\Baselibs\Schema\Column;
use Sematico\Baselibs\Schema\Index;
use Sematico\Baselibs\Schema\Foreign_Key;
use Mockery as m;

beforeEach(
	function () {
		$this->grammar = new Grammar();
		$this->schema  = m::mock( Schema::class );
	}
);

it(
	'compiles columns into MySQL column definition string',
	function () {
		$columns = [
			m::mock(
				Column::class,
				[
					'get_name'              => 'id',
					'get_type'              => 'INT',
					'get_length'            => null,
					'is_unsigned'           => true,
					'is_nullable'           => false,
					'is_auto_increment'     => true,
					'get_default'           => null,
					'is_default_expression' => false,
				]
			),
			m::mock(
				Column::class,
				[
					'get_name'              => 'name',
					'get_type'              => 'VARCHAR',
					'get_length'            => 255,
					'is_unsigned'           => false,
					'is_nullable'           => false,
					'is_auto_increment'     => false,
					'get_default'           => 'John Doe',
					'is_default_expression' => false,
				]
			),
		];

		$reflection = new \ReflectionClass( $this->grammar );
		$method     = $reflection->getMethod( 'compile_columns' );
		$method->setAccessible( true );
		$result = $method->invoke( $this->grammar, $columns );

		expect( $result )->toBe( "id INT UNSIGNED NOT NULL AUTO_INCREMENT,\nname VARCHAR(255) NOT NULL DEFAULT 'John Doe'" );
	}
);

it(
	'compiles indexes into MySQL index definition string',
	function () {
		$indexes = [
			m::mock(
				Index::class,
				[
					'get_type'   => 'PRIMARY KEY',
					'get_name'   => 'primary',
					'get_column' => 'id',
				]
			),
			m::mock(
				Index::class,
				[
					'get_type'   => 'INDEX',
					'get_name'   => 'name_index',
					'get_column' => 'name',
				]
			),
		];

		$reflection = new \ReflectionClass( $this->grammar );
		$method     = $reflection->getMethod( 'compile_indexes' );
		$method->setAccessible( true );
		$result = $method->invoke( $this->grammar, $indexes );

		expect( $result )->toBe( "PRIMARY KEY (`id`),\nINDEX `name_index` (`name`)" );
	}
);

it(
	'compiles foreign keys into MySQL foreign key definition string',
	function () {
		$foreign_keys = [
			m::mock(
				Foreign_Key::class,
				[
					'get_name'             => 'fk_user_id',
					'get_column'           => 'user_id',
					'get_reference_table'  => 'users',
					'get_reference_column' => 'id',
					'get_on_delete'        => 'CASCADE',
					'get_on_update'        => 'CASCADE',
				]
			),
		];

		$reflection = new \ReflectionClass( $this->grammar );
		$method     = $reflection->getMethod( 'compile_foreign_keys' );
		$method->setAccessible( true );
		$result = $method->invoke( $this->grammar, $foreign_keys );

		expect( $result )->toBe( 'CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE' );
	}
);

it(
	'compiles schema into MySQL create table statement',
	function () {
		$this->schema->shouldReceive( 'get_table_name' )->andReturn( 'users' );
		$this->schema->shouldReceive( 'get_columns' )->andReturn(
			[
				m::mock(
					Column::class,
					[
						'get_name'          => 'id',
						'get_type'          => 'INT',
						'get_length'        => null,
						'is_unsigned'       => true,
						'is_nullable'       => false,
						'is_auto_increment' => true,
						'get_default'       => null,
					]
				),
			]
		);
		$this->schema->shouldReceive( 'get_indexes' )->andReturn(
			[
				m::mock(
					Index::class,
					[
						'get_type'   => 'PRIMARY KEY',
						'get_name'   => 'primary',
						'get_column' => 'id',
					]
				),
			]
		);
		$this->schema->shouldReceive( 'get_foreign_keys' )->andReturn( [] );
		$this->schema->shouldReceive( 'get_wpdb->get_charset_collate' )->andReturn( 'utf8mb4_unicode_ci' );

		$result = $this->grammar->compile( $this->schema );

		expect( $result )->toBe( "CREATE TABLE users (\nid INT UNSIGNED NOT NULL AUTO_INCREMENT,\nPRIMARY KEY (`id`)\n) utf8mb4_unicode_ci;" );
	}
);
