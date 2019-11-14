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
	 * Produce XML to output.
	 */
	public function render_sitemap() {
		global $wp_query;

		$sitemap  = get_query_var( 'sitemap' );
		$sub_type = get_query_var( 'sub_type' );
		$paged    = get_query_var( 'paged' );

		$sub_types = $this->get_object_sub_types();

		$this->sub_type = $sub_types[ $sub_type ]->name;
		if ( empty( $paged ) ) {
			$paged = 1;
		}

		if ( $this->slug === $sitemap ) {

			if ( ! isset( $sub_types[ $sub_type ] ) ) {
				// Invalid sub type.
				$wp_query->set_404();
				status_header( 404 );

				return;
			}

			$url_list = $this->get_url_list( $paged );
			$renderer = new Core_Sitemaps_Renderer();
			$renderer->render_sitemap( $url_list );

			exit;
		}
	}

	/**
	 * Get a URL list for a taxonomy sitemap.
	 *
	 * @param int $page_num Page of results.
	 *
	 * @return array $url_list List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num = 1 ) {
		$type = $this->sub_type; // Find the query_var for sub_type.
		if ( empty( $type ) ) {
			return;
		}

		// Get all of the taxonomies that are registered.
		$taxonomies = $this->get_object_sub_types();

		$url_list = array();

		foreach ( $taxonomies as $taxonomy ) {
			// if the query_var matches a taxonomy name, get the terms for that tax.
			if ( $type === $taxonomy->name ) {
				$taxonomy_terms = get_terms(
					array(
						'fields'     => 'ids',
						'taxonomy'   => $taxonomy->name,
						'orderby'    => 'term_order',
						'hide_empty' => true,
					)
				);

				// Loop through the terms and get the latest post stored in each.
				foreach ( $taxonomy_terms as $term ) {
					$last_modified = get_posts(
						array(
							'tax_query'              => array(
								array(
									'taxonomy' => $taxonomy->name,
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
	 * Return all public, registered taxonomies.
	 */
	public function get_object_sub_types() {
		$taxonomy_types = get_taxonomies( array( 'public' => true ), 'objects' );

		/**
		 * Filter the list of taxonomy object sub types available within the sitemap.
		 *
		 * @param array $taxonomy_types List of registered object sub types.
		 *
		 * @since 0.1.0
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

}
