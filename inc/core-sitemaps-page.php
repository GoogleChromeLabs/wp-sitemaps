<?php
defined( 'ABSPATH' ) or die();

/**
 * Calculate the sitemap page number the post belongs to.
 *
 * @param $post_id integer Post ID.
 *
 * @return int Sitemap Page pagination number.
 */
function core_sitemaps_page_calculate_page_num( $post_id ) {
	// TODO this lookup might need to be more refined and set min/max
	return 1 + (int) floor( $post_id / CORE_SITEMAPS_POSTS_PER_PAGE );
}

/**
 * Regoster the Sitemap Page custom post-type.
 */
function core_sitemaps_page_register() {
	$labels = array(
		'name'          => _x( 'Sitemap Pages', 'Sitemap Page General Name', 'core-sitemaps' ),
		'singular_name' => _x( 'Sitemap Page', 'Sitemap Page Singular Name', 'core-sitemaps' ),
	);
	$args   = array(
		'label'           => __( 'Sitemap Page', 'core-sitemaps' ),
		'description'     => __( 'Bucket of sitemap links', 'core-sitemaps' ),
		'labels'          => $labels,
		'supports'        => array( 'editor', 'custom-fields' ),
		'can_export'      => false,
		'rewrite'         => false,
		'capability_type' => 'post',
	);
	register_post_type( CORE_SITEMAPS_CPT_PAGE, $args );
}

/**
 * Get the Sitemap Page for a pagination number.
 *
 * @param string $post_type Registered post-type.
 * @param int $page_num Sitemap Page pagination number.
 *
 * @return bool|int[]|WP_Post[] Zero or more Post objects of the type CORE_SITEMAPS_CPT_PAGE.
 */
function core_sitemaps_page_lookup( $post_type, $page_num ) {
	$page_query            = new WP_Query();
	$registered_post_types = core_sitemaps_registered_post_types();
	if ( false === isset( $registered_post_types[ $post_type ] ) ) {
		return false;
	}
	$query_result = $page_query->query( array(
		'post_type'  => CORE_SITEMAPS_CPT_PAGE,
		'meta_query' => array(
			array(
				'key'   => 'page_num',
				'value' => $page_num,
			),
			array(
				'key'   => 'post_type',
				'value' => $post_type,
			),
		),
	) );

	return $query_result;
}
