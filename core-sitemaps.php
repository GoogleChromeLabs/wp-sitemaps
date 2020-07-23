<?php
/**
 * Core Sitemaps Plugin.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @copyright 2019 The Core Sitemaps Contributors
 * @license   GNU General Public License, version 2
 * @link      https://github.com/GoogleChromeLabs/wp-sitemaps
 *
 * Plugin Name:       Core Sitemaps
 * Plugin URI:        https://github.com/GoogleChromeLabs/wp-sitemaps
 * Description:       A feature plugin to integrate basic XML Sitemaps in WordPress Core
 * Author:            Core Sitemaps Plugin Contributors
 * Author URI:        https://github.com/GoogleChromeLabs/wp-sitemaps/graphs/contributors
 * Text Domain:       core-sitemaps
 * Domain Path:       /languages
 * Requires at least: 5.4
 * Requires PHP:      5.6
 * Version:           0.4.3
 */

// Do not load plugin if WordPress core already has sitemap support.
if ( function_exists( 'wp_get_sitemaps' ) || function_exists( 'wp_get_sitemap_providers' ) ) {
	return;
}

// The limit for how many sitemaps to include in an index.
const WP_SITEMAPS_MAX_SITEMAPS    = 50000;
const WP_SITEMAPS_REWRITE_VERSION = '2020-04-29';

// Limit the number of URLs included in a sitemap.
if ( ! defined( 'WP_SITEMAPS_MAX_URLS' ) ) {
	define( 'WP_SITEMAPS_MAX_URLS', 2000 );
}

require_once __DIR__ . '/inc/class-wp-sitemaps.php';
require_once __DIR__ . '/inc/class-wp-sitemaps-provider.php';
require_once __DIR__ . '/inc/class-wp-sitemaps-index.php';
require_once __DIR__ . '/inc/class-wp-sitemaps-registry.php';
require_once __DIR__ . '/inc/class-wp-sitemaps-renderer.php';
require_once __DIR__ . '/inc/class-wp-sitemaps-stylesheet.php';
require_once __DIR__ . '/inc/providers/class-wp-sitemaps-posts.php';
require_once __DIR__ . '/inc/providers/class-wp-sitemaps-taxonomies.php';
require_once __DIR__ . '/inc/providers/class-wp-sitemaps-users.php';
require_once __DIR__ . '/inc/functions.php';

// Boot the sitemaps system.
add_action( 'init', 'wp_sitemaps_get_server' );

/**
 * Plugin activation hook.
 *
 * Adds and flushes rewrite rules.
 */
function wp_sitemaps_plugin_activation() {
	$sitemaps = new WP_Sitemaps();
	$sitemaps->register_rewrites();
	flush_rewrite_rules( false );
}

register_activation_hook( __FILE__, 'wp_sitemaps_plugin_activation' );

/**
 * Plugin deactivation hook.
 *
 * Adds and flushes rewrite rules.
 */
function wp_sitemaps_plugin_deactivation() {
	$sitemaps = new WP_Sitemaps();
	$sitemaps->unregister_rewrites();
	flush_rewrite_rules( false );
}

register_deactivation_hook( __FILE__, 'wp_sitemaps_plugin_deactivation' );
