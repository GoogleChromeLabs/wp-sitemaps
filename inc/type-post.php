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

/**
 * @param         $post_id
 * @param WP_Post $post
 *
 * @return bool|int|WP_Error
 */
function core_sitemaps_type_post_on_save( $post_id, WP_Post $post ) {
	$bucket_num   = core_sitemaps_page_calculate_bucket_num( $post_id );
	$query_result = core_sitemaps_bucket_lookup( 'post', $bucket_num );
	if ( false === $query_result ) {
		return false;
	}

	if ( count( $query_result ) < 1 ) {
		return core_sitemaps_bucket_insert( $post, $bucket_num );
	}

	foreach ( $query_result as $page ) {
		return core_sitemaps_bucket_update( $post, $page );
	}

	// Well that's awkward.
	return false;
}

/**
 * When a post is deleted, remove page from sitemaps page.
 *
 * @param $post_id integer Post ID.
 *
 * @return bool @see wp_update_post()
 */
function core_sitemaps_type_post_on_delete( $post_id ) {
	$page_num     = core_sitemaps_page_calculate_bucket_num( $post_id );
	$query_result = core_sitemaps_bucket_lookup( 'post', $page_num );
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
