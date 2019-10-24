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

const CORE_SITEMAPS_TEXT_DOMAIN = 'core-sitemaps';
const CORE_SITEMAPS_CPT_PAGE    = 'core_sitemaps_page';

require_once __DIR__ . '/inc/class-core-sitemaps-page.php';

/**
 * Bootstrapping.
 */
function core_sitemaps_init() {
	core_sitemaps_register_ctp();
}

add_action( 'init', 'core_sitemaps_init', 10 );

function core_sitemaps_register_ctp() {
	$labels = array(
		'name'          => _x( 'Sitemap Pages', 'Sitemap Page General Name', CORE_SITEMAPS_TEXT_DOMAIN ),
		'singular_name' => _x( 'Sitemap Page', 'Sitemap Page Singular Name', CORE_SITEMAPS_TEXT_DOMAIN ),
	);
	$args   = array(
		'label'           => __( 'Sitemap Page', CORE_SITEMAPS_TEXT_DOMAIN ),
		'description'     => __( 'Bucket of sitemap links', CORE_SITEMAPS_TEXT_DOMAIN ),
		'labels'          => $labels,
		'supports'        => array( 'editor', 'custom-fields' ),
		'can_export'      => false,
		'rewrite'         => false,
		'capability_type' => 'post',
	);
	register_post_type( CORE_SITEMAPS_CPT_PAGE, $args );
}
