<?php
/**
 * Class file for the Core_Sitemaps_Provider class.
 * This class is a base class for other sitemap providers to extend and contains shared functionality.
 *
 * @package Core_Sitemaps
 */

/**
 * Class Core_Sitemaps_Provider
 */
class Core_Sitemaps_Provider {
	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $object_type = '';

	/**
	 * Sub type name.
	 *
	 * @var string
	 */
	protected $sub_type = '';

	/**
	 * Sitemap name
	 *
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Sitemap route
	 *
	 * Regex pattern used when building the route for a sitemap.
	 *
	 * @var string
	 */
	public $route = '';

	/**
	 * Sitemap slug
	 *
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	public $slug = '';

	/**
	 * Get a URL list for a post type sitemap.
	 *
	 * @param int $page_num Page of results.
	 *
	 * @return array $url_list List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num ) {
		$type = $this->sub_type;
		if ( empty( $type ) ) {
			$type = $this->object_type;
		}
		$query = new WP_Query( array(
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'post_type'      => $type,
			'posts_per_page' => CORE_SITEMAPS_POSTS_PER_PAGE,
			'paged'          => $page_num,
			'no_found_rows'  => true,
		) );

		$posts = $query->get_posts();

		$url_list = array();

		foreach ( $posts as $post ) {
			$url_list[] = array(
				'loc'     => get_permalink( $post ),
				'lastmod' => mysql2date( DATE_W3C, $post->post_modified_gmt, false ),
			);
		}

		/**
		 * Filter the list of URLs for a sitemap before rendering.
		 *
		 * @param array  $url_list List of URLs for a sitemap.
		 * @param string $type Name of the post_type.
		 * @param int    $page_num Page of results.
		 *
		 * @since 0.1.0
		 */
		return apply_filters( 'core_sitemaps_post_url_list', $url_list, $type, $page_num );
	}

	/**
	 * Query for the add_rewrite_rule. Must match the number of Capturing Groups in the route regex.
	 *
	 * @return string Valid add_rewrite_rule query.
	 */
	public function rewrite_query() {
		return 'index.php?sitemap=' . $this->name . '&paged=$matches[1]';
	}
}
