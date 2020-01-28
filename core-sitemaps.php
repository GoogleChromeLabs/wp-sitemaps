<?php
/**
 * Main setup.
 *
 * @package         Core_Sitemaps
 */

/**
 * Core Sitemaps Plugin.
 *
 * @package   Core_Sitemaps
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
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Version:           0.1.0
 */

// The limit for how many sitemaps to include in an index.
const CORE_SITEMAPS_MAX_SITEMAPS    = 50000;
const CORE_SITEMAPS_REWRITE_VERSION = '2019-11-15a';

// Limit the number of URLs included in as sitemap.
if ( ! defined( 'CORE_SITEMAPS_MAX_URLS' ) ) {
	define( 'CORE_SITEMAPS_MAX_URLS', 2000 );
}

require_once __DIR__ . '/inc/class-core-sitemaps.php';
require_once __DIR__ . '/inc/class-core-sitemaps-provider.php';
require_once __DIR__ . '/inc/class-core-sitemaps-index.php';
require_once __DIR__ . '/inc/class-core-sitemaps-posts.php';
require_once __DIR__ . '/inc/class-core-sitemaps-registry.php';
require_once __DIR__ . '/inc/class-core-sitemaps-renderer.php';
require_once __DIR__ . '/inc/class-core-sitemaps-stylesheet.php';
require_once __DIR__ . '/inc/class-core-sitemaps-taxonomies.php';
require_once __DIR__ . '/inc/class-core-sitemaps-users.php';
require_once __DIR__ . '/inc/functions.php';

global $core_sitemaps;

$core_sitemaps = new Core_Sitemaps();
$core_sitemaps->bootstrap();
