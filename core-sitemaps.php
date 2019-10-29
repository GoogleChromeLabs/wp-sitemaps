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

require_once __DIR__ . '/inc/class-sitemaps-index.php';
require_once __DIR__ . '/inc/class-sitemaps-posts.php';
require_once __DIR__ . '/inc/class-sitemaps-registry.php';

/**
 *
 * A helper function to initiate actions, hooks and other features needed.
 *
 * @uses add_action()
 * @uses add_filter()
 */
function core_sitemaps_bootstrap() {
	$core_sitemaps_index = new Core_Sitemaps_Index();
	add_action( 'init', array( $core_sitemaps_index, 'url_rewrites' ), 99 );
	add_filter( 'redirect_canonical', array( $core_sitemaps_index, 'redirect_canonical' ) );
	add_filter( 'template_include', array( $core_sitemaps_index, 'output_sitemap' ) );

	$core_sitemaps_posts = new Core_Sitemaps_Posts();
	add_action( 'init', array( $core_sitemaps_posts, 'url_rewrites' ), 99 );
	add_filter( 'template_include', array( $core_sitemaps_posts, 'template' ) );
}

add_filter( 'init', 'core_sitemaps_bootstrap' );
