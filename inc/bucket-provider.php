<?php

/**
 * Register the Sitemap Bucket custom post-type.
 */
function core_sitemaps_bucket_register() {
	$labels = array(
		'name'          => _x( 'Sitemap Buckets', 'Sitemap Bucket General Name', 'core-sitemaps' ),
		'singular_name' => _x( 'Sitemap Bucket', 'Sitemap Bucket Singular Name', 'core-sitemaps' ),
	);
	$args   = array(
		'label'           => __( 'Sitemap Bucket', 'core-sitemaps' ),
		'description'     => __( 'Bucket of sitemap links', 'core-sitemaps' ),
		'labels'          => $labels,
		'supports'        => array( 'editor', 'custom-fields' ),
		'can_export'      => false,
		'rewrite'         => false,
		'capability_type' => 'post',
	);
	register_post_type( CORE_SITEMAPS_CPT_BUCKET, $args );
}
