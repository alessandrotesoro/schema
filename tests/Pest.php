<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

use Sematico\Baselibs\Schema\Tests\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

$is_integration_tests = in_array( '--group=integration', $_SERVER['argv'], true );

if ( ! $is_integration_tests ) {
	uses()->group( 'unit' )->in( 'Unit' );
	WP_Mock::bootstrap();
}

if ( $is_integration_tests ) {
	pest()->extend( TestCase::class )->in( 'Integration' );

	$_tests_dir = getenv( 'WP_TESTS_DIR' );

	if ( ! $_tests_dir ) {
		$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
	}

	// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
	$_phpunit_polyfills_path = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
	if ( false !== $_phpunit_polyfills_path ) {
		define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_phpunit_polyfills_path );
	}

	if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
		echo "Could not find {$_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit( 1 );
	}

	// Give access to tests_add_filter() function.
	require_once "{$_tests_dir}/includes/functions.php";

	/**
	 * Manually load the plugin being tested.
	 */
	function _manually_load_plugin() {
	}

	tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

	// Start up the WP testing environment.
	require "{$_tests_dir}/includes/bootstrap.php";

	uses()->group( 'integration' )->in( 'Integration' );
}

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend(
	'toBeOne',
	function () {
		return $this->toBe( 1 );
	}
);

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

beforeAll(
	function () {
		WP_Mock::setUp();
	}
);

afterAll(
	function () {
		WP_Mock::tearDown();
	}
);
