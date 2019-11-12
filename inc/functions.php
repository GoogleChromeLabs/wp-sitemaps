<?php
/**
 * Core sitemap public functions.
 *
 * @package Core_Sitemaps
 */

/**
 * Get a list of sitemaps.
 *
 * @return array $sitemaps A list of registered sitemap providers.
 */
function core_sitemaps_get_sitemaps() {
	global $core_sitemaps;

	$sitemaps = $core_sitemaps->registry->get_sitemaps();

	return $sitemaps;
}

/**
 * Helper to get a specific bucket object.
 *
 * @param string  $object_type    Main type of object stored in the bucket.
 * @param string  $object_subtype Object subtype for a bucket, e.g. post_type.
 * @param integer $page_num       The page number of the bucket being requested.
 * @return WP_Post|false A WP_Post object if found. False if none found or buckets not in use.
 */
function core_sitemaps_get_bucket( $object_type, $object_subtype, $page_num ) {
	global $core_sitemaps;

	return $core_sitemaps->buckets->get_bucket( $object_type, $object_subtype, $page_num );
}

/**
 * Helper to save a specific bucket object.
 *
 * @param string  $object_type    Main type of object stored in the bucket.
 * @param string  $object_subtype Object subtype for a bucket, e.g. post_type.
 * @param integer $page_num       The page number of the bucket being requested.
 * @param array   $url_list       A list of url entry data for a sitemap.
 * @return int|WP_Error The value 0 or WP_Error on failure. The post ID on success.
 */
function core_sitemaps_save_bucket( $object_type, $object_subtype, $page_num, $url_list ) {
	global $core_sitemaps;

	return $core_sitemaps->buckets->save_bucket( $object_type, $object_subtype, $page_num, $url_list );
}
