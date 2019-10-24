<?php

defined( 'ABSPATH' ) or die();

/**
 * Sets content of of the sitemap url item with the post info.
 *
 * @param WP_Post $post Post object.
 *
 * @return array Associative array of url entry data.
 */
function core_sitemaps_url_content( $post ) {
	return array(
		'loc'        => get_permalink( $post ),
		// TODO check gmt
		'lastmod'    => mysql2date( DATE_W3C, $post->post_modified, false ),
		'priority'   => core_sitemaps_url_priority( $post ),
		'changefreq' => core_sitemaps_url_changefreq( $post ),
	);
}

/**
 * Set the priority attribute of the url element.
 *
 * @param $post WP_Post Reference post object.
 *
 * @return string priority value.
 */
function core_sitemaps_url_priority( $post ) {
	// Fixme: placeholder
	return '0.5';
}

/**
 * Set the changefreq attribute of the url element.
 *
 * @param $post WP_Post Reference post object.
 *
 * @return string changefreq value.
 */
function core_sitemaps_url_changefreq( $post ) {
	// Fixme: placeholder
	return 'monthly';
}
