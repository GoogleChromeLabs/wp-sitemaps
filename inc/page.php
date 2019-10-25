<?php
/**
 * Each sitemaps has total posts / 50,000 pages.
 */
defined( 'ABSPATH' ) || die();

function core_sitemaps_page_calculate_num( $post_id ) {
	return 1 + (int) floor( $post_id / 50000 );
}

function core_sitemaps_page_render( $post_type, $page_num ) {
	$buckets_per_page = 50000 / CORE_SITEMAPS_POSTS_PER_BUCKET;
	$start_bucket     = 1 + ( $page_num - 1 ) * $buckets_per_page;
	$query_result     = core_sitemaps_bucket_lookup( $post_type, $start_bucket, $buckets_per_page );
	// render each bucket.
	foreach ( $query_result as $bucket ) {
		core_sitemaps_bucket_render( $bucket );
	}
}
