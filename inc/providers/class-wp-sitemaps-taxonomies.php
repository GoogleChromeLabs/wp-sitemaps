<?php
/**
 * Sitemaps: WP_Sitemaps_Taxonomies class
 *
 * Builds the sitemaps for the 'taxonomy' object type.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Taxonomies XML sitemap provider.
 *
 * @since 5.5.0
 */
class WP_Sitemaps_Taxonomies extends WP_Sitemaps_Provider {
	/**
	 * WP_Sitemaps_Taxonomies constructor.
	 *
	 * @since 5.5.0
	 */
	public function __construct() {
		$this->name        = 'taxonomies';
		$this->object_type = 'term';
	}

	/**
	 * Returns all public, registered taxonomies.
	 *
	 * @since 5.5.0
	 *
	 * @return array Map of registered taxonomy objects keyed by their name.
	 */
	public function get_object_subtypes() {
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

		/**
		 * Filter the list of taxonomy object subtypes available within the sitemap.
		 *
		 * @since 5.5.0
		 *
		 * @param array $taxonomies Map of registered taxonomy objects keyed by their name.
		 */
		return apply_filters( 'wp_sitemaps_taxonomies', $taxonomies );
	}

	/**
	 * Gets a URL list for a taxonomy sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param int    $page_num Page of results.
	 * @param string $taxonomy Optional. Taxonomy name. Default empty.
	 * @return array $url_list Array of URLs for a sitemap.
	 */
	public function get_url_list( $page_num, $taxonomy = '' ) {
		$supported_types = $this->get_object_subtypes();

		// Bail early if the queried taxonomy is not supported.
		if ( ! isset( $supported_types[ $taxonomy ] ) ) {
			return array();
		}

		$url_list = array();

		// Offset by how many terms should be included in previous pages.
		$offset = ( $page_num - 1 ) * wp_sitemaps_get_max_urls( $this->object_type );

		$args = array(
			'fields'                 => 'ids',
			'taxonomy'               => $taxonomy,
			'orderby'                => 'term_order',
			'number'                 => wp_sitemaps_get_max_urls( $this->object_type ),
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
			foreach ( $taxonomy_terms->terms as $term ) {
				$sitemap_entry = array(
					'loc' => get_term_link( $term ),
				);

				/**
				 * Filters the sitemap entry for an individual term.
				 *
				 * @since 5.5.0
				 *
				 * @param array   $sitemap_entry Sitemap entry for the term.
				 * @param WP_Term $term          Term object.
				 * @param string  $taxonomy      Taxonomy name.
				 */
				$sitemap_entry = apply_filters( 'wp_sitemaps_taxonomies_entry', $sitemap_entry, $term, $taxonomy );
				$url_list[] = $sitemap_entry;
			}
		}

		/**
		 * Filters the array of URLs for a sitemap. before rendering.
		 *
		 * @since 5.5.0
		 *
		 * @param array  $url_list Array of URLs for a sitemap.
		 * @param string $taxonomy Taxonomy name.
		 * @param int    $page_num Page of results.
		 */
		return apply_filters( 'wp_sitemaps_taxonomies_url_list', $url_list, $taxonomy, $page_num );
	}

	/**
	 * Gets the max number of pages available for the object type.
	 *
	 * @since 5.5.0
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @return int Total number of pages.
	 */
	public function max_num_pages( $taxonomy = '' ) {
		if ( empty( $taxonomy ) ) {
			return 0;
		}

		$term_count = wp_count_terms( $taxonomy, array( 'hide_empty' => true ) );

		return (int) ceil( $term_count / wp_sitemaps_get_max_urls( $this->object_type ) );
	}
}
