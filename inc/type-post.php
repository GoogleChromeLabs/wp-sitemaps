<?php
/**
 * Posts Sitemap (for post-type posts).
 */

defined( 'ABSPATH' ) || die();

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
 * @param int     $post_id Post object ID.
 * @param WP_Post $post Post object.
 *
 * @return bool|int|WP_Error Return wp_insert_post() / wp_update_post() output; or false if no bucket exists.
 */
function core_sitemaps_type_post_on_save( $post_id, $post ) {
	$bucket_num   = core_sitemaps_page_calculate_bucket_num( $post_id );
	$query_result = core_sitemaps_bucket_lookup( 'post', $bucket_num );
	if ( false === $query_result ) {
		return false;
	}

	if ( count( $query_result ) < 1 ) {
		// Fixme: handle WP_Error.
		return core_sitemaps_bucket_insert( $post, $bucket_num );
	}

	/** @noinspection LoopWhichDoesNotLoopInspection */
	foreach ( $query_result as $page ) {
		// Fixme: handle WP_Error.
		return core_sitemaps_bucket_update( $post, $page );
	}

	// Well that's awkward.
	return false;
}

/**
 * When a post is deleted, remove page from sitemaps page.
 *
 * @param int $post_id Post ID.
 *
 * @return bool @see wp_update_post()
 */
function core_sitemaps_type_post_on_delete( $post_id ) {
	$bucket_num   = core_sitemaps_page_calculate_bucket_num( $post_id );
	$query_result = core_sitemaps_bucket_lookup( 'post', $bucket_num );
	if ( false === $query_result ) {
		return false;
	}

	/** @noinspection LoopWhichDoesNotLoopInspection */
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

/**
 * Render a post_type sitemap.
 */
function core_sitemaps_type_post_render() {
	global $wpdb;
	$post_type  = 'post';
	$max_id     = $wpdb->get_var( $wpdb->prepare( "SELECT MAX(ID) FROM $wpdb->posts WHERE post_type = %s", $post_type ) );
	$page_count = core_sitemaps_page_calculate_num( $max_id );

	// Fixme: We'd never have to render more than one page though.
	for ( $p = 1; $p <= $page_count; $p++ ) {
		core_sitemaps_render_header();
		core_sitemaps_page_render( $post_type, $p );
		core_sitemaps_render_footer();
	}
}
