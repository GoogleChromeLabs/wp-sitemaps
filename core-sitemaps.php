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

const CORE_SITEMAPS_CPT_PAGE       = 'core_sitemaps_page';
const CORE_SITEMAPS_POSTS_PER_PAGE = 2000;

require_once __DIR__ . '/inc/core-sitemaps-page.php';
require_once __DIR__ . '/inc/core-sitemaps-type-post.php';
require_once __DIR__ . '/inc/core-sitemaps-url.php';

/**
 * Bootstrapping.
 */
function core_sitemaps_init() {
	core_sitemaps_page_register();

	$register_post_types = core_sitemaps_registered_post_types();
	foreach ( $register_post_types as $post_type ) {
		call_user_func( $register_post_types[ $post_type ] );
	}
}

/**
 * @return mixed|void
 */
function core_sitemaps_registered_post_types() {
	return apply_filters( 'core_sitemaps_register_post_types', array() );
}

add_action( 'init', 'core_sitemaps_init', 10 );
