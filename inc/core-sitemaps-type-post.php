<?php
defined( 'ABSPATH' ) or die();

// Register the a sitemap for the post post-type.
add_filter( 'core_sitemaps_register_post_types', static function ( $post_types ) {
	$post_types['post'] = 'core_sitemaps_type_post_register';

	return $post_types;
} );

/**
 * Registration for the Post Sitemaps hooks.
 */
function core_sitemaps_type_post_register() {
	add_action( 'save_post_post', 'core_sitemaps_type_post_on_save', 10, 2 );
	add_action( 'after_delete_post', 'core_sitemaps_type_post_on_delete' );
}

function core_sitemaps_type_post_on_save( $post_id, WP_Post $post ) {
	$page_num     = core_sitemaps_page_calculate_page_num( $post_id );
	$query_result = core_sitemaps_page_lookup( 'post', $page_num );
	if ( false === $query_result ) {
		return false;
	}

	if ( $query_result < 1 ) {
		return core_sitemaps_page_insert( $post, $page_num );
	}

	foreach ( $query_result as $page ) {
		return core_sitemaps_page_update( $post, $page );
	}

	// Well that's awkward.
	return false;
}

/**
 * Update a sitemap page with post info.
 *
 * @param WP_Post $post Post object.
 * @param WP_Post $page Sitemap Page object.
 *
 * @return int|WP_Error @see wp_update_post()
 */
function core_sitemaps_page_update( $post, $page ) {
	$items              = json_decode( $page->post_content, true );
	$items[ $post->ID ] = core_sitemaps_url_content( $post );
	$page->post_content = wp_json_encode( $items );

	return wp_update_post( $page );
}

/**
 * Create a sitemaps page with post info.
 *
 * @param WP_Post $post Post object.
 * @param int $page_num Sitemap Page pagination number.
 *
 * @return int|WP_Error @see wp_update_post()
 */
function core_sitemaps_page_insert( $post, $page_num ) {
	$args = array(
		'post_type'    => CORE_SITEMAPS_CPT_PAGE,
		'post_content' => wp_json_encode( array(
			$post->ID => core_sitemaps_url_content( $post ),
		) ),
		'meta_input'   => array(
			'page_num'  => $page_num,
			'post_type' => $post->post_type,
		),
		'post_status'  => 'publish',
	);

	return wp_insert_post( $args );
}

/**
 * When a post is deleted, remove page from sitemaps page.
 *
 * @param $post_id integer Post ID.
 *
 * @return bool @see wp_update_post()
 */
function core_sitemaps_type_post_on_delete( $post_id ) {
	$page_num     = core_sitemaps_page_calculate_page_num( $post_id );
	$query_result = core_sitemaps_page_lookup( 'post', $page_num );
	if ( false === $query_result ) {
		return false;
	}

	foreach ( $query_result as $page ) {
		$items = json_decode( $page->post_content );
		if ( isset( $items[ $post_id ] ) ) {
			unset( $items[ $post_id ] );
		}
		$page->post_content = wp_json_encode( $items );

		return wp_update_post( $page );
	}

	return false;
}
