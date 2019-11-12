<?php
/**
 * Core Sitemaps: Core_Sitemaps_Buckets class
 *
 * @package Core_Sitemaps
 * @since 0.1.0
 */

/**
 * Core class to manage sitemap buckets.
 *
 * @since 0.1.0
 */
class Core_Sitemaps_Buckets {

	/**
	 * Custom post type key.
	 *
	 * @var string
	 */
	const POST_TYPE = 'core_sitemaps_bucket';

	/**
	 * Register the sitemap buckets custom post type.
	 */
	public function register_bucket_post_type() {
		// Post type arguments for our custom post type.
		$args = array(
			'labels'             => array(
				'name'          => _x( 'Sitemap Buckets', 'Post type general name', 'core-sitemaps' ),
				'singular_name' => _x( 'Sitemap Bucket', 'Post type singular name', 'core-sitemaps' ),
			),
			'public'             => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Helper to get a specific bucket object.
	 *
	 * @param string  $object_type    Main type of object stored in the bucket.
	 * @param string  $object_subtype Object subtype for a bucket, e.g. post_type.
	 * @param integer $page_num       The page number of the bucket being requested.
	 * @return WP_Post|false A WP_Post object if found. False if none found or buckets not in use.
	 */
	public function get_bucket( $object_type, $object_subtype, $page_num ) {
		/**
		 * Filter the use of bucket caching.
		 *
		 * @param bool Whether buckets are in use. Default true.
		 */
		$use_buckets = apply_filters( 'core_sitemaps_use_buckets', true );

		if ( ! $use_buckets ) {
			return false;
		}

		$bucket_name = $this->get_bucket_name( $object_type, $object_subtype, $page_num );

		$query_args = array(
			'post_type' => self::POST_TYPE,
			'post_name' => $bucket_name,
			'posts_per_page' => 1,
			'no_found_rows' => true,
		);

		$buckets = get_posts( $query_args );

		if ( empty( $buckets ) ) {
			return false;
		}

		return $buckets[0];
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
	public function save_bucket( $object_type, $object_subtype, $page_num, $url_list ) {
		/**
		 * Documented in Core_Sitemaps_Buckets::get_bucket();
		 */
		$use_buckets = apply_filters( 'core_sitemaps_use_buckets', true );

		if ( ! $use_buckets ) {
			return 0;
		}

		$bucket = core_sitemaps_get_bucket( $object_type, $object_subtype, $page_num, $url_list );

		// Sort the url_list by ID.
		ksort( $url_list );

		// Prepare bucket metadata.
		$bucket_meta = array(
			'count'  => count( $url_list ),
			'min_ID' => key( array_slice( $url_list, 0, 1, true) ),
			'max_ID' => key( array_slice( $url_list, -1, 1, true) ),
		);

		if ( $bucket ) {
			return wp_update_post(
				array(
					'ID'           => $bucket->ID,
					'post_content' => json_encode( $url_list ),
					'meta_input'   => array(
						'core_sitemap_bucket_meta' => $bucket_meta,
					),
				)
			);
		}

		$bucket_name = $this->get_bucket_name( $object_type, $object_subtype, $page_num );

		return wp_insert_post(
			array(
				'post_type'    => self::POST_TYPE,
				'post_name'    => $bucket_name,
				'post_content' => json_encode( $url_list ),
				'post_status'  => 'publish',
				'meta_input'   => array(
					'core_sitemap_bucket_meta' => $bucket_meta,
				),
			)
		);
	}

	/**
	 * Helper function to generate a bucket name.
	 *
	 * @param string  $object_type    Main type of object stored in the bucket.
	 * @param string  $object_subtype Object subtype for a bucket, e.g. post_type.
	 * @param integer $page_num       The page number of the bucket being requested.
	 * @return string The post title for a sitemap bucket.
	 */
	public function get_bucket_name( $object_type, $object_subtype, $page_num ) {
		$bucket_name = 'sitemap-' . $object_type;

		if ( ! empty( $object_subtype ) ) {
			$bucket_name = $bucket_name . '-' . $object_subtype;
		}

		if ( ! empty( $page_num ) ) {
			$bucket_name = $bucket_name . '-' . $page_num;
		}

		return $bucket_name;
	}
}
