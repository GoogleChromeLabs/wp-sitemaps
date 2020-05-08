<?php
/**
 * Sitemaps: Core_Sitemaps_Provider class
 *
 * This class is a base class for other sitemap providers to extend and contains shared functionality.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Class Core_Sitemaps_Provider.
 *
 * @since 5.5.0
 */
class Core_Sitemaps_Provider {
	/**
	 * Post type name.
	 *
	 * @since 5.5.0
	 *
	 * @var string
	 */
	protected $object_type = '';

	/**
	 * Subtype name.
	 *
	 * @since 5.5.0
	 *
	 * @var string
	 */
	protected $sub_type = '';

	/**
	 * Gets a URL list for a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param int    $page_num Page of results.
	 * @param string $type     Optional. Post type name. Default empty.
	 * @return array $url_list List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num, $type = '' ) {
		return array();
	}

	/**
	 * Returns the name of the object type being queried.
	 *
	 * @since 5.5.0
	 *
	 * @return string Name of the object type.
	 */
	public function get_queried_type() {
		$type = $this->sub_type;

		if ( empty( $type ) ) {
			return $this->object_type;
		}

		return $type;
	}

	/**
	 * Gets the max number of pages available for the object type.
	 *
	 * @since 5.5.0
	 *
	 * @param string $type Optional. Object type. Default is null.
	 * @return int Total number of pages.
	 */
	public function max_num_pages( $type = '' ) {
		if ( empty( $type ) ) {
			$type = $this->get_queried_type();
		}

		$query = new WP_Query(
			array(
				'fields'                 => 'ids',
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'post_type'              => $type,
				'posts_per_page'         => core_sitemaps_get_max_urls( $this->object_type ),
				'paged'                  => 1,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			)
		);

		return isset( $query->max_num_pages ) ? $query->max_num_pages : 1;
	}

	/**
	 * Sets the object sub_type.
	 *
	 * @since 5.5.0
	 *
	 * @param string $sub_type The name of the object subtype.
	 * @return bool Returns true on success.
	 */
	public function set_sub_type( $sub_type ) {
		$this->sub_type = $sub_type;

		return true;
	}

	/**
	 * Gets data about each sitemap type.
	 *
	 * @since 5.5.0
	 *
	 * @return array List of sitemap types including object subtype name and number of pages.
	 */
	public function get_sitemap_type_data() {
		$sitemap_data = array();

		$sitemap_types = $this->get_object_sub_types();

		foreach ( $sitemap_types as $type ) {
			// Handle lists of post-objects.
			if ( isset( $type->name ) ) {
				$type = $type->name;
			}

			$sitemap_data[] = array(
				'name'   => $type,
				'pages' => $this->max_num_pages( $type ),
			);
		}

		return $sitemap_data;
	}

	/**
	 * Lists sitemap pages exposed by this provider.
	 *
	 * The returned data is used to populate the sitemap entries of the index.
	 *
	 * @since 5.5.0
	 *
	 * @return array List of sitemaps.
	 */
	public function get_sitemap_entries() {
		$sitemaps = array();

		$sitemap_types = $this->get_sitemap_type_data();

		foreach ( $sitemap_types as $type ) {
			for ( $page = 1; $page <= $type['pages']; $page ++ ) {
				$loc        = $this->get_sitemap_url( $type['name'], $page );
				$sitemaps[] = array(
					'loc' => $loc,
				);
			}
		}

		return $sitemaps;
	}

	/**
	 * Gets the URL of a sitemap entry.
	 *
	 * @since 5.5.0
	 *
	 * @param string $name The name of the sitemap.
	 * @param int    $page The page of the sitemap.
	 * @return string The composed URL for a sitemap entry.
	 */
	public function get_sitemap_url( $name, $page ) {
		/* @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;

		$basename = sprintf(
			'/wp-sitemap-%1$s.xml',
			// Accounts for cases where name is not included, ex: sitemaps-users-1.xml.
			implode( '-', array_filter( array( $this->object_type, $name, (string) $page ) ) )
		);

		$url = home_url( $basename );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$url = add_query_arg(
				array(
					'sitemap'          => $this->object_type,
					'sitemap-sub-type' => $name,
					'paged'            => $page,
				),
				home_url( '/' )
			);
		}

		return $url;
	}

	/**
	 * Returns the list of supported object subtypes exposed by the provider.
	 *
	 * By default this is the sub_type as specified in the class property.
	 *
	 * @since 5.5.0
	 *
	 * @return array List: containing object types or false if there are no subtypes.
	 */
	public function get_object_sub_types() {
		if ( ! empty( $this->sub_type ) ) {
			return array( $this->sub_type );
		}

		/**
		 * To prevent complexity in code calling this function, such as `get_sitemaps()` in this class,
		 * an iterable type is returned. The value false was chosen as it passes empty() checks and
		 * as semantically this provider does not provide subtypes.
		 *
		 * @link https://github.com/GoogleChromeLabs/wp-sitemaps/pull/72#discussion_r347496750
		 */
		return array( false );
	}
}
