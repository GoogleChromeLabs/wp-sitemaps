<?php
/**
 * WordPress PHPUnit bootstrap file.
 */

namespace HM\Tests\Phpunit;

$_root_dir = getcwd();
require_once $_root_dir . '/vendor/autoload.php';

$_tests_dir = getenv( 'WP_PHPUNIT__DIR' );

require_once $_tests_dir . '/includes/functions.php';

/**
 * Disable update checks for core, themes, and plugins.
 *
 * No need for this work to happen when spinning up tests.
 */
function _remove_automated_checks() {
	remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );
	remove_action( 'wp_update_themes', 'wp_update_themes' );
	remove_action( 'wp_update_plugins', 'wp_update_plugins' );

	remove_action( 'admin_init', '_maybe_update_core' );
	remove_action( 'admin_init', 'wp_maybe_auto_update' );
	remove_action( 'admin_init', 'wp_auto_update_core' );
	remove_action( 'admin_init', '_maybe_update_themes' );
	remove_action( 'admin_init', '_maybe_update_plugins' );

	remove_action( 'wp_version_check', 'wp_version_check' );
}

/**
 * Load any plugins we might need.
 */
tests_add_filter( 'muplugins_loaded', function() use ( $_root_dir ) {
	_remove_automated_checks();
} );

/**
 * Hardcode timezone for tests.
 *
 * @param bool $_ Not used.
 *
 * @return string New timezone.
 */
tests_add_filter( 'pre_option_timezone_string', function( $_ ) {
	return 'Europe/London';
} );

require $_tests_dir . '/includes/bootstrap.php';

require_once $_root_dir . '/tests/phpunit/class-test-case.php';
