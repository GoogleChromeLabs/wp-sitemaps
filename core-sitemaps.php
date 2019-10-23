<?php
/**
 * Plugin Name:     Core Sitemaps
 * Plugin URI:      https://github.com/humanmade/core-sitemaps
 * Description:     A feature plugin to integrate basic XML Sitemaps in WordPress Core
 * Author:          Core Sitemaps Plugin Contributors
 * Author URI:      https://github.com/humanmade/core-sitemaps/graphs/contributors
 * Text Domain:     core-sitemaps
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Core_Sitemaps
 */

defined( 'ABSPATH' ) or die();

// require_once __DIR__ . '/inc/class-core-sitemaps-page.php';

/**
 * Bootstrapping.
 */
function core_sitemaps_init() {
}

add_action( 'init', 'core_sitemaps_init', 10 );
