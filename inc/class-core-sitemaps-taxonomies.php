<?php
/**
 * Taxonomies sitemap.
 *
 * @package Core_Sitemaps
 */

/**
 * Class Core_Sitemaps_Taxonomies.
 * Builds the sitemap pages for Taxonomies.
 */
class Core_Sitemaps_Taxonomies extends Core_Sitemaps_Provider {
	/**
	 * Core_Sitemaps_Taxonomies constructor.
	 */
	public function __construct() {
		$this->object_type = 'taxonomy';
		$this->route       = '^sitemap-taxonomies-([A-z]+)-?([0-9]+)?\.xml$';
		$this->slug        = 'taxonomies';
	}

	/**
	 * Get a URL list for a taxonomy sitemap.
	 *
	 * @param int $page_num Page of results.
	 * @return array $url_list List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num ) {
		// Find the query_var for sub_type.
		$type = $this->sub_type;

		if ( empty( $type ) ) {
			return array();
		}

		$url_list = array();

		// Offset by how many terms should be included in previous pages.
		$offset = ( $page_num - 1 ) * CORE_SITEMAPS_POSTS_PER_PAGE;

		$args = array(
			'fields'                 => 'ids',
			'taxonomy'               => $type,
			'orderby'                => 'term_order',
			'number'                 => CORE_SITEMAPS_POSTS_PER_PAGE,
			'offset'                 => $offset,
			'hide_empty'             => true,

			/*
			 * Limits aren't included in queries when hierarchical is set to true (by default).
			 *
			 * @link: https://github.com/WordPress/WordPress/blob/5.3/wp-includes/class-wp-term-query.php#L558-L567
			 */
			'hierarchical'           => false,
			'update_term_meta_cache' => false,
		);

		$taxonomy_terms = new WP_Term_Query( $args );

		if ( ! empty( $taxonomy_terms->terms ) ) {
			// Loop through the terms and get the latest post stored in each.
			foreach ( $taxonomy_terms->terms as $term ) {
				$last_modified = get_posts(
					array(
						'tax_query'              => array(
							array(
								'taxonomy' => $type,
								'field'    => 'term_id',
								'terms'    => $term,
							),
						),
						'posts_per_page'         => '1',
						'orderby'                => 'date',
						'order'                  => 'DESC',
						'no_found_rows'          => true,
						'update_post_term_cache' => false,
						'update_post_meta_cache' => false,
					)
				);

				// Extract the data needed for each term URL in an array.
				$url_list[] = array(
					'loc'     => get_term_link( $term ),
					'lastmod' => mysql2date( DATE_W3C, $last_modified[0]->post_modified_gmt, false ),
				);
			}
		}

		/**
		 * Filter the list of URLs for a sitemap before rendering.
		 *
		 * @since 0.1.0
		 *
		 * @param array  $url_list List of URLs for a sitemap.
		 * @param string $type     Name of the taxonomy_type.
		 * @param int    $page_num Page of results.
		 */
		return apply_filters( 'core_sitemaps_taxonomies_url_list', $url_list, $type, $page_num );
	}

	/**
	 * Return all public, registered taxonomies.
	 */
	public function get_object_sub_types() {
		$taxonomy_types = get_taxonomies( array( 'public' => true ), 'objects' );

		/**
		 * Filter the list of taxonomy object sub types available within the sitemap.
		 *
		 * @since 0.1.0
		 *
		 * @param array $taxonomy_types List of registered object sub types.
		 */
		return apply_filters( 'core_sitemaps_taxonomies', $taxonomy_types );
	}

	/**
	 * Query for the Taxonomies add_rewrite_rule.
	 *
	 * @return string Valid add_rewrite_rule query.
	 */
	public function rewrite_query() {
		return 'index.php?sitemap=' . $this->slug . '&sub_type=$matches[1]&paged=$matches[2]';
	}

	/**
	 * Sitemap Index query for determining the number of pages.
	 *
	 * @param string $type Taxonomy name.
	 * @return int Total number of pages.
	 */
	public function max_num_pages( $type = '' ) {
		if ( empty( $type ) ) {
			$type = $this->get_queried_type();
		}

		$args = array(
			'fields'     => 'ids',
			'taxonomy'   => $type,
			'orderby'    => 'term_order',
			'number'     => CORE_SITEMAPS_POSTS_PER_PAGE,
			'paged'      => 1,
			'hide_empty' => true,
		);

		$query = new WP_Term_Query( $args );

		return isset( $query->max_num_pages ) ? $query->max_num_pages : 1;
	}
}
