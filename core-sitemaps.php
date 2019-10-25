<?php
/**
 * @package Core_Sitemaps
 * @copyright 2019 The Core Sitemaps Contributors
 * @license   GNU General Public License, version 2
 * @link      https://github.com/GoogleChromeLabs/wp-sitemaps
 *
 * Plugin Name:     Core Sitemaps
 * Plugin URI:      https://github.com/GoogleChromeLabs/wp-sitemaps
 * Description:     A feature plugin to integrate basic XML Sitemaps in WordPress Core
 * Author:          Core Sitemaps Plugin Contributors
 * Author URI:      https://github.com/GoogleChromeLabs/wp-sitemaps/graphs/contributors
 * Text Domain:     core-sitemaps
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Core_Sitemaps
 */

defined( 'ABSPATH' ) || die();

const CORE_SITEMAPS_CPT_BUCKET       = 'core_sitemaps_bucket';
const CORE_SITEMAPS_POSTS_PER_BUCKET = 2000;

require_once __DIR__ . '/inc/bucket.php';
require_once __DIR__ . '/inc/page.php';
require_once __DIR__ . '/inc/type-post.php';
require_once __DIR__ . '/inc/url.php';

/**
 * Bootstrapping.
 */
function core_sitemaps_init() {
	// Fixme: temporarily unhooking template.
	core_sitemaps_bucket_register();

	$register_post_types = core_sitemaps_registered_post_types();
	foreach ( array_keys( $register_post_types ) as $post_type ) {
		call_user_func( $register_post_types[ $post_type ] );
	}
}

add_action( 'init', 'core_sitemaps_init', 10 );

/**
 * Provides the `core_sitemaps_register_post_types` filter to register post types for inclusion in the sitemap.
 *
 * @return array Associative array.  Key is the post-type name; Value is a registration callback function.
 */
function core_sitemaps_registered_post_types() {
	return apply_filters( 'core_sitemaps_register_post_types', array() );
}

/**
 * Temporary header rendering, obviously we'd want to do an XML DOMDocument.
 */
function core_sitemaps_render_header() {
	echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
}

/**
 * Temporary footer rendering, probably won't be required soon.
 */
function core_sitemaps_render_footer() {
	echo '</urlset>';
}
