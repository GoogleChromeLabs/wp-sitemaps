<?php
/**
 * WordPress PHPUnit bootstrap file.
 *
 * @package   Core_Sitemaps
 * @copyright 2019 The Core Sitemaps Contributors
 * @license   GNU General Public License, version 2
 * @link      https://github.com/GoogleChromeLabs/wp-sitemaps
 */

$sitemaps_root_dir = dirname( __DIR__ );
require_once $sitemaps_root_dir . '/vendor/autoload.php';

$sitemaps_tests_dir = getenv( 'WP_PHPUNIT__DIR' );

/**
 * Include is dynamically defined.
 *
 * @noinspection PhpIncludeInspection
 */
require_once $sitemaps_tests_dir . '/includes/functions.php';

/**
 * Disable update checks for core, themes, and plugins.
 *
 * No need for this work to happen when spinning up tests.
 */
function sitemaps_remove_automated_checks() {
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
 * Remove automated checks during test load.
 */
tests_add_filter(
	'muplugins_loaded',
	static function () {
		sitemaps_remove_automated_checks();
	}
);

/**
 * Load any plugins we might need.
 */
tests_add_filter(
	'muplugins_loaded',
	static function () {
		require dirname( dirname( __FILE__ ) ) . '/core-sitemaps.php';
	}
);

/**
 * Hard code timezone for tests.
 */
tests_add_filter(
	'pre_option_timezone_string',
	static function () {
		return 'UTC';
	}
);

/**
 * Include is dynamically defined.
 *
 * @noinspection PhpIncludeInspection
 */
require $sitemaps_tests_dir . '/includes/bootstrap.php';
