<?php

/**
 * Class Core_Sitemaps_Categories.
 * Builds the sitemap pages for Categories.
 */
class Core_Sitemaps_Categories extends Core_Sitemaps_Provider {
	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $object_type = 'taxonomy';

	/**
	 * Sub type name.
	 *
	 * @var string
	 */
	protected $sub_type = '';

	/**
	 * Sitemap name
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	public $name = 'taxonomies';

	/**
	 * Sitemap route.
	 *
	 * Regex pattern used when building the route for a sitemap.
	 *
	 * @var string
	 */
	public $route = '^sitemap-taxonomies-([A-z]+)-?([0-9]+)?\.xml$';

	/**
	 * Sitemap slug.
	 *
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	public $slug = 'taxonomies';

	/**
	 * Get a URL list for a user sitemap.
	 *
	 * @param string $object_type Name of the object_type.
	 * @param int    $page_num Page of results.
	 * @return array $url_list List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num = 1 ) {

		$terms = $this->get_object_sub_types();

		$url_list = array();

		foreach ( $terms as $term ) {
			$last_modified = get_posts( array(
				'taxonomy'       => $term->term_id,
				'post_type'      => 'post',
				'posts_per_page' => '1',
				'orderby'        => 'date',
				'order'          => 'DESC',
			) );

			$url_list[] = array(
				'loc' => get_term_link( $term->term_id ),
				'lastmod' => mysql2date( DATE_W3C, $last_modified[0]->post_modified_gmt, false ),
			);
		}
		/**
		 * Filter the list of URLs for a sitemap before rendering.
		 *
		 * @since 0.1.0
		 *
		 * @param array  $url_list    List of URLs for a sitemap.
		 * @param string $type.       Name of the taxonomy_type.
		 * @param int    $page_num    Page of results.
		 */
		return apply_filters( 'core_sitemaps_taxonomies_url_list', $url_list, $type, $page_num );
	}

	/**
	 * Produce XML to output.
	 */
	public function render_sitemap() {
		global $wp_query;

		$sitemap   = get_query_var( 'sitemap' );
		$sub_type  = get_query_var( 'sub_type' );
		$paged     = get_query_var( 'paged' );
		$sub_types = $this->get_object_sub_types();

		if ( ! isset( $sub_types[ $sub_type ] ) ) {
			// Invalid sub type.
			$wp_query->set_404();
			status_header( 404 );

			return;
		}

		$this->sub_type = $sub_types[ $sub_type ]->name;
		if ( empty( $paged ) ) {
			$paged = 1;
		}

		if ( $this->name === $sitemap ) {
			$url_list = $this->get_url_list( $paged );
			$renderer = new Core_Sitemaps_Renderer();
			$renderer->render_sitemap( $url_list );

			exit;
		}
	}

	/**
	 * Return all public, registered taxonomies.
	 */
	public function get_object_sub_types() {

		$taxonomy_types = get_taxonomies( array( 'public' => true ), 'objects' );

		/**
		 * Filter the list of post object sub types available within the sitemap.
		 *
		 * @param array $post_types List of registered object sub types.
		 *
		 * @since 0.1.0
		 */
		return apply_filters( 'core_sitemaps_taxonomies', $taxonomy_types );
	}

	/**
	 * Query for the Posts add_rewrite_rule.
	 *
	 * @return string Valid add_rewrite_rule query.
	 */
	public function rewrite_query() {
		return 'index.php?sitemap=' . $this->name . '&sub_type=$matches[1]&paged=$matches[2]';
	}

}
