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

defined( 'ABSPATH' ) || die();

const CORE_SITEMAPS_CPT_BUCKET       = 'core_sitemaps_bucket';
const CORE_SITEMAPS_POSTS_PER_BUCKET = 2000;

require_once __DIR__ . '/inc/page.php';
require_once __DIR__ . '/inc/type-post.php';
require_once __DIR__ . '/inc/url.php';

/**
 * Bootstrapping.
 */
function core_sitemaps_init() {
	core_sitemaps_bucket_register();

	$register_post_types = core_sitemaps_registered_post_types();
	foreach ( $register_post_types as $post_type ) {
		call_user_func( $register_post_types[ $post_type ] );
	}
}

/**
 * Provides the `core_sitemaps_register_post_types` filter to register post types for inclusion in the sitemap.
 *
 * @return array Associative array.  Key is the post-type name; Value is a registration callback function.
 */
function core_sitemaps_registered_post_types() {
	return apply_filters( 'core_sitemaps_register_post_types', array() );
}

add_action( 'init', 'core_sitemaps_init', 10 );
