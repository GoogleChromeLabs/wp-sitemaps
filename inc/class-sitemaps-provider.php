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
	 * Get the latest post for each term.
	 *
	 * @param string $term Name of the term.
	 *
	 * @return $content Query result.
	 */
	public function get_latest_post_terms( $term ) {
		$query = new WP_Query();

		$content = $query->query(
			array(
				'cat'            => $term->term_id,
				'post_type'      => 'post',
				'posts_per_page' => '1',
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		return $content;
	}

	/**
	 * Get content for a page.
	 *
	 * @param string $object_type Name of the object_type.
	 * @param int    $page_num Page of results.
	 *
	 * @return int[]|WP_Post[] Query result.
	 */
	public function get_content_per_page( $object_type, $page_num = 1 ) {
		$query = new WP_Query();

		return $query->query(
			array(
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'post_type'      => $object_type,
				'posts_per_page' => CORE_SITEMAPS_POSTS_PER_PAGE,
				'paged'          => $page_num,
			)
		);
	}
}
