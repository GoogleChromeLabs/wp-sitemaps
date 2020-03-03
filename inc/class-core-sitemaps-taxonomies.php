<?php
/**
 * Sitemaps: Core_Sitemaps_Taxonomies class
 *
 * This class builds the sitemaps for the 'taxonomy' object type.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since x.x.x
 */

/**
 * Taxonomies XML sitemap provider.
 */
class Core_Sitemaps_Taxonomies extends Core_Sitemaps_Provider {
	/**
	 * Core_Sitemaps_Taxonomies constructor.
	 */
	public function __construct() {
		$this->object_type = 'taxonomy';
		$this->route       = '^wp-sitemap-taxonomies-([A-z]+)-?([0-9]+)?\.xml$';
		$this->slug        = 'taxonomies';
	}

	/**
	 * Get a URL list for a taxonomy sitemap.
	 *
	 * @param int    $page_num Page of results.
	 * @param string $type     Optional. Taxonomy type name. Default ''.
	 * @return array $url_list List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num, $type = '' ) {
		// Find the query_var for sub_type.
		if ( ! $type ) {
			$type = $this->get_queried_type();
		}

		// Bail early if we don't have a taxonomy type.
		if ( empty( $type ) ) {
			return array();
		}

		$supported_types = $this->get_object_sub_types();

		// Bail early if the queried taxonomy is not a supported type.
		if ( ! isset( $supported_types[ $type ] ) ) {
			return array();
		}

		$url_list = array();

		// Offset by how many terms should be included in previous pages.
		$offset = ( $page_num - 1 ) * core_sitemaps_get_max_urls( $this->slug );

		$args = array(
			'fields'                 => 'ids',
			'taxonomy'               => $type,
			'orderby'                => 'term_order',
			'number'                 => core_sitemaps_get_max_urls( $this->slug ),
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

		/**
		 * Fires when retrieving the URL list for a taxonomy sitemap.
		 *
		 * @param WP_Term_Query $query Query instance.
		 */
		do_action( 'core_sitemaps_taxonomy_query', $taxonomy_terms );

		if ( ! empty( $taxonomy_terms->terms ) ) {
			// Loop through the terms and get the latest post stored in each.
			foreach ( $taxonomy_terms->terms as $term ) {
				$query = new WP_Query(
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

				/**
				 * Fires when retrieving the last modified for a given term.
				 *
				 * @param WP_Query $query Query instance.
				 */
				do_action( 'core_sitemaps_term_last_modified_query', $query );

				$last_modified = $query->get_posts();

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
		 * @param array $taxonomy_types List of registered taxonomy type names.
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

		$term_count = wp_count_terms( $type, array( 'hide_empty' => true ) );

		return (int) ceil( $term_count / core_sitemaps_get_max_urls( $this->slug ) );
	}
}
