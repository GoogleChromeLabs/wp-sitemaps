<?php
/**
 * Builds the sitemap pages for Categories.
 *
 * @package Core_Sitemaps
 */

/**
 * Class Core_Sitemaps_Categories.
 */
class Core_Sitemaps_Categories extends Core_Sitemaps_Provider {
	/**
	 * Core_Sitemaps_Categories constructor.
	 */
	public function __construct() {
		$this->object_type = 'category';
		$this->route       = '^sitemap-categories-?([0-9]+)?\.xml$';
		$this->slug        = 'categories';
	}

	/**
	 * Get a URL list for a user sitemap.
	 *
	 * @param int $page_num Page of results.
	 * @return array $url_list List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num = 1 ) {
		$terms = get_terms(
			[
				'taxonomy' => 'category',
			]
		);

		$url_list = array();

		foreach ( $terms as $term ) {
			$last_modified = get_posts(
				array(
					'cat'            => $term->term_id,
					'post_type'      => 'post',
					'posts_per_page' => '1',
					'orderby'        => 'date',
					'order'          => 'DESC',
				)
			);

			$url_list[] = array(
				'loc'     => get_category_link( $term->term_id ),
				'lastmod' => mysql2date( DATE_W3C, $last_modified[0]->post_modified_gmt, false ),
			);
		}

		/**
		 * Filter the list of URLs for a sitemap before rendering.
		 *
		 * @since 0.1.0
		 * @param array  $url_list    List of URLs for a sitemap.
		 * @param string $object_type Name of the post_type.
		 * @param int    $page_num    Page of results.
		 */
		return apply_filters( 'core_sitemaps_categories_url_list', $url_list, 'category', $page_num );
	}

	/**
	 * Produce XML to output.
	 *
	 * @noinspection PhpUnusedPrivateMethodInspection
	 */
	public function render_sitemap() {
		$sitemap = get_query_var( 'sitemap' );
		$paged   = get_query_var( 'paged' );
		if ( empty( $paged ) ) {
			$paged = 1;
		}
		if ( 'categories' === $sitemap ) {
			$url_list = $this->get_url_list( $paged );
			$renderer = new Core_Sitemaps_Renderer();
			$renderer->render_sitemap( $url_list );
			exit;
		}
	}
}
