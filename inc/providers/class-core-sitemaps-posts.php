<?php
/**
 * Sitemaps: Core_Sitemaps_Posts class
 *
 * Builds the sitemaps for the 'post' object type.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Posts XML sitemap provider.
 *
 * @since 5.5.0
 */
class Core_Sitemaps_Posts extends Core_Sitemaps_Provider {
	/**
	 * Core_Sitemaps_Posts constructor.
	 *
	 * @since 5.5.0
	 */
	public function __construct() {
		$this->name        = 'posts';
		$this->object_type = 'post';
	}

	/**
	 * Returns the public post types, which excludes nav_items and similar types.
	 * Attachments are also excluded. This includes custom post types with public = true.
	 *
	 * @since 5.5.0
	 *
	 * @return array Map of registered post type objects keyed by their name.
	 */
	public function get_object_subtypes() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $post_types['attachment'] );

		/**
		 * Filters the list of post object sub types available within the sitemap.
		 *
		 * @since 5.5.0
		 *
		 * @param array $post_types Map of registered post type objects keyed by their name.
		 */
		return apply_filters( 'core_sitemaps_post_types', $post_types );
	}

	/**
	 * Gets a URL list for a post type sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param int    $page_num  Page of results.
	 * @param string $post_type Optional. Post type name. Default empty.
	 * @return array List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num, $post_type = '' ) {
		if ( ! $post_type ) {
			$post_type = $this->get_queried_type();
		}

		// Return an empty array if the type is not supported.
		$supported_types = $this->get_object_subtypes();

		if ( ! isset( $supported_types[ $post_type ] ) ) {
			return array();
		}

		$query = new WP_Query(
			array(
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'post_type'              => $post_type,
				'posts_per_page'         => core_sitemaps_get_max_urls( $this->object_type ),
				'post_status'            => array( 'publish' ),
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
		if ( 'page' === $post_type && 1 === $page_num && 'posts' === get_option( 'show_on_front' ) ) {
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
		 * Filters the list of URLs for a sitemap before rendering.
		 *
		 * @since 5.5.0
		 *
		 * @param array  $url_list  List of URLs for a sitemap.
		 * @param string $post_type Name of the post_type.
		 * @param int    $page_num  Page number of the results.
		 */
		return apply_filters( 'core_sitemaps_posts_url_list', $url_list, $post_type, $page_num );
	}

	/**
	 * Gets the max number of pages available for the object type.
	 *
	 * @since 5.5.0
	 *
	 * @param string $post_type Optional. Post type name. Default empty.
	 * @return int Total number of pages.
	 */
	public function max_num_pages( $post_type = '' ) {
		if ( empty( $post_type ) ) {
			$post_type = $this->get_queried_type();
		}

		$query = new WP_Query(
			array(
				'fields'                 => 'ids',
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'post_type'              => $post_type,
				'posts_per_page'         => core_sitemaps_get_max_urls( $this->object_type ),
				'paged'                  => 1,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			)
		);

		return isset( $query->max_num_pages ) ? $query->max_num_pages : 1;
	}
}
