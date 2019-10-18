<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Core_Sitemaps
 */

$core_sitemaps_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $core_sitemaps_tests_dir ) {
	$core_sitemaps_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $core_sitemaps_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $core_sitemaps_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // WPCS: XSS ok.
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $core_sitemaps_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function core_sitemaps_manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/core-sitemaps.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $core_sitemaps_tests_dir . '/includes/bootstrap.php';
