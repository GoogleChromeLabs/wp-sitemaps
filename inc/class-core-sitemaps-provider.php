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
		$object_type = $this->object_type;

		$query       = new WP_Query( array(
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'post_type'              => $object_type,
			'posts_per_page'         => CORE_SITEMAPS_POSTS_PER_PAGE,
			'paged'                  => $page_num,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
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
		 * @since 0.1.0
		 *
		 * @param string $object_type Name of the post_type.
		 * @param int    $page_num    Page of results.
		 *
		 * @param array  $url_list    List of URLs for a sitemap.
		 */
		return apply_filters( 'core_sitemaps_post_url_list', $url_list, $object_type, $page_num );
	}
}
