<?php

use Sematico\Baselibs\Schema\Index;

it(
	'can create an index with a default name',
	function () {
		$index = new Index( 'column_name' );
		expect( $index->get_name() )->toBe( 'idx_column_name' );
		expect( $index->get_column() )->toBe( 'column_name' );
	}
);

it(
	'can create an index with a custom name',
	function () {
		$index = new Index( 'column_name', 'custom_name' );
		expect( $index->get_name() )->toBe( 'custom_name' );
		expect( $index->get_column() )->toBe( 'column_name' );
	}
);

it(
	'can check if the index is unique',
	function () {
		$index = new Index( 'column_name' );
		expect( $index->is_unique() )->toBeFalse();

		$index->unique( true );
		expect( $index->is_unique() )->toBeTrue();
	}
);

it(
	'can check if the index is full-text',
	function () {
		$index = new Index( 'column_name' );
		expect( $index->is_full_text() )->toBeFalse();

		$index->full_text( true );
		expect( $index->is_full_text() )->toBeTrue();
	}
);

it(
	'can check if the index is primary',
	function () {
		$index = new Index( 'column_name' );
		expect( $index->is_primary() )->toBeFalse();

		$index->primary( true );
		expect( $index->is_primary() )->toBeTrue();
	}
);

it(
	'can get the index type',
	function () {
		$index = new Index( 'column_name' );
		expect( $index->get_type() )->toBe( 'INDEX' );

		$index->unique( true );
		expect( $index->get_type() )->toBe( 'UNIQUE' );

		$index->full_text( true );
		expect( $index->get_type() )->toBe( 'FULLTEXT' );

		$index->primary( true );
		expect( $index->get_type() )->toBe( 'PRIMARY KEY' );
	}
);
