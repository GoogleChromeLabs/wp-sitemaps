<?php
/**
 * Sitemaps: WP_Sitemaps_Posts class
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
class WP_Sitemaps_Posts extends WP_Sitemaps_Provider {
	/**
	 * WP_Sitemaps_Posts constructor.
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
	 * @return array Map of registered post type objects (WP_Post_Type) keyed by their name.
	 */
	public function get_object_subtypes() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $post_types['attachment'] );

		/**
		 * Filters the list of post object sub types available within the sitemap.
		 *
		 * @since 5.5.0
		 *
		 * @param array $post_types Map of registered post type objects (WP_Post_Type) keyed by their name.
		 */
		return apply_filters( 'wp_sitemaps_post_types', $post_types );
	}

	/**
	 * Gets a URL list for a post type sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param int    $page_num  Page of results.
	 * @param string $post_type Optional. Post type name. Default empty.
	 * @return array $url_list Array of URLs for a sitemap.
	 */
	public function get_url_list( $page_num, $post_type = '' ) {
		// Bail early if the queried post type is not supported.
		$supported_types = $this->get_object_subtypes();

		if ( ! isset( $supported_types[ $post_type ] ) ) {
			return array();
		}

		/**
		 * Filters the post type URL list query arguments.
		 *
		 * Allows modification of the URL list query arguments before querying.
		 *
		 * @see WP_Query for a full list of arguments
		 *
		 * @since 5.5.0
		 *
		 * @param array  $args      An array of WP_Query arguments.
		 * @param string $post_type The post type string.
		 */
		$args = apply_filters(
			'wp_sitemaps_posts_url_list_query_args',
			array(
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'post_type'              => $post_type,
				'posts_per_page'         => wp_sitemaps_get_max_urls( $this->object_type ),
				'post_status'            => array( 'publish' ),
				'paged'                  => $page_num,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			),
			$post_type
		);

		$url_list = apply_filters(
			'core_pre_sitemaps_post_url_list_query',
			null,
			$post_type,
			$args
		);

		if ( null !== $url_list ) {
			return $url_list;
		}

		$query = new WP_Query( $args );

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
			$sitemap_entry = array(
				'loc' => get_permalink( $post ),
			);

			/**
			 * Filters the sitemap entry for an individual post.
			 *
			 * @since 5.5.0
			 *
			 * @param array   $sitemap_entry Sitemap entry for the post.
			 * @param WP_Post $post          Post object.
			 * @param string  $post_type     Name of the post_type.
			 */
			$sitemap_entry = apply_filters( 'wp_sitemaps_posts_entry', $sitemap_entry, $post, $post_type );
			$url_list[] = $sitemap_entry;
		}

		/**
		 * Filters the array of URLs for a sitemap before rendering.
		 *
		 * @since 5.5.0
		 *
		 * @param array  $url_list  Array of URLs for a sitemap.
		 * @param string $post_type Name of the post_type.
		 * @param int    $page_num  Page number of the results.
		 */
		return apply_filters( 'wp_sitemaps_posts_url_list', $url_list, $post_type, $page_num );
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
			return 0;
		}

		/**
		 * Filters the query arguments for calculating the maximum number of pages.
		 *
		 * Allows modification of the "maximum number of pages" query arguments before querying.
		 *
		 * @see WP_Query for a full list of arguments
		 *
		 * @since 5.5.0
		 *
		 * @param array  $args      An array of WP_Query arguments.
		 * @param string $post_type The post type string.
		 */
		$args = apply_filters(
			'wp_sitemaps_posts_max_num_pages_query_args',
			array(
				'fields'                 => 'ids',
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'post_type'              => $post_type,
				'posts_per_page'         => wp_sitemaps_get_max_urls( $this->object_type ),
				'paged'                  => 1,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			),
			$post_type
		);

		$query = new WP_Query( $args );

		return isset( $query->max_num_pages ) ? $query->max_num_pages : 1;
	}
}
