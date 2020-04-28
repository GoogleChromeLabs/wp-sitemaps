<?php
/**
 * Sitemaps: Core_Sitemaps_Posts class
 *
 * This class builds the sitemaps for the 'post' object type.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since x.x.x
 */

/**
 * Posts XML sitemap provider.
 */
class Core_Sitemaps_Posts extends Core_Sitemaps_Provider {
	/**
	 * Core_Sitemaps_Posts constructor.
	 */
	public function __construct() {
		$this->object_type = 'posts';
	}

	/**
	 * Return the public post types, which excludes nav_items and similar types.
	 * Attachments are also excluded. This includes custom post types with public = true
	 *
	 * @return array $post_types List of registered object sub types.
	 */
	public function get_object_sub_types() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $post_types['attachment'] );

		/**
		 * Filter the list of post object sub types available within the sitemap.
		 *
		 * @since 0.1.0
		 * @param array $post_types List of registered object sub types.
		 */
		return apply_filters( 'core_sitemaps_post_types', $post_types );
	}

	/**
	 * Get a URL list for a post type sitemap.
	 *
	 * @param int    $page_num Page of results.
	 * @param string $type     Optional. Post type name. Default ''.
	 * @return array $url_list List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num, $type = '' ) {
		if ( ! $type ) {
			$type = $this->get_queried_type();
		}

		// Return an empty array if the type is not supported.
		$supported_types = $this->get_object_sub_types();

		if ( ! isset( $supported_types[ $type ] ) ) {
			return array();
		}

		$query = new WP_Query(
			array(
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'post_type'              => $type,
				'posts_per_page'         => core_sitemaps_get_max_urls( $this->object_type ),
				'paged'                  => $page_num,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			)
		);

		/**
		 * Returns an array of posts.
		 *
		 * @var array<int, \WP_Post> $posts
		 */
		$posts = $query->get_posts();

		$url_list = array();

		/*
		 * Add a URL for the homepage in the pages sitemap.
		 * Shows only on the first page if the reading settings are set to display latest posts.
		 */
		if ( 'page' === $type && 1 === $page_num && 'posts' === get_option( 'show_on_front' ) ) {
			// Extract the data needed for home URL to add to the array.
			$url_list[] = array(
				'loc' => home_url(),
			);
		}

		foreach ( $posts as $post ) {
			$url_list[] = array(
				'loc' => get_permalink( $post ),
			);
		}

		/**
		 * Filter the list of URLs for a sitemap before rendering.
		 *
		 * @since 0.1.0
		 *
		 * @param array  $url_list List of URLs for a sitemap.
		 * @param string $type     Name of the post_type.
		 * @param int    $page_num Page of results.
		 */
		return apply_filters( 'core_sitemaps_posts_url_list', $url_list, $type, $page_num );
	}
}
