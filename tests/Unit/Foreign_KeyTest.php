<?php

use Sematico\Baselibs\Schema\Foreign_Key;

it(
	'can create a foreign key with a name and column',
	function () {
		$foreignKey = new Foreign_Key( 'user_id', 'fk_user_id' );
		expect( $foreignKey->get_name() )->toBe( 'fk_user_id' );
		expect( $foreignKey->get_column() )->toBe( 'user_id' );
	}
);

it(
	'can create a foreign key with a default name',
	function () {
		$foreignKey = new Foreign_Key( 'user_id' );
		expect( $foreignKey->get_name() )->toBe( 'fk_user_id' );
		expect( $foreignKey->get_column() )->toBe( 'user_id' );
	}
);

it(
	'can set the reference table and column',
	function () {
		$foreignKey = new Foreign_Key( 'user_id' );
		$foreignKey->reference( 'users', 'id' );
		expect( $foreignKey->get_reference_table() )->toBe( 'users' );
		expect( $foreignKey->get_reference_column() )->toBe( 'id' );
	}
);

it(
	'can set the reference table',
	function () {
		$foreignKey = new Foreign_Key( 'user_id' );
		$foreignKey->reference_table( 'users' );
		expect( $foreignKey->get_reference_table() )->toBe( 'users' );
	}
);

it(
	'can set the reference column',
	function () {
		$foreignKey = new Foreign_Key( 'user_id' );
		$foreignKey->reference_column( 'id' );
		expect( $foreignKey->get_reference_column() )->toBe( 'id' );
	}
);

it(
	'can set the on delete action',
	function () {
		$foreignKey = new Foreign_Key( 'user_id' );
		$foreignKey->on_delete( 'CASCADE' );
		expect( $foreignKey->get_on_delete() )->toBe( 'CASCADE' );
	}
);

it(
	'can set the on update action',
	function () {
		$foreignKey = new Foreign_Key( 'user_id' );
		$foreignKey->on_update( 'CASCADE' );
		expect( $foreignKey->get_on_update() )->toBe( 'CASCADE' );
	}
);
