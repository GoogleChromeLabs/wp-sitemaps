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
	 * Object type name (e.g. 'post', 'term', 'user').
	 *
	 * @since 5.5.0
	 *
	 * @var string
	 */
	protected $object_type = '';

	/**
	 * Object subtype name.
	 *
	 * For example, this should be a post type name for object type 'post' or
	 * a taxonomy name for object type 'term').
	 *
	 * @since 5.5.0
	 *
	 * @var string
	 */
	protected $object_subtype = '';

	/**
	 * Gets a URL list for a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param int    $page_num       Page of results.
	 * @param string $object_subtype Optional. Object subtype name. Default empty.
	 * @return array $url_list List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num, $object_subtype = '' ) {
		return array();
	}

	/**
	 * Returns the name of the object type or object subtype being queried.
	 *
	 * @since 5.5.0
	 *
	 * @return string Object subtype if set, otherwise object type.
	 */
	public function get_queried_type() {
		if ( empty( $this->object_subtype ) ) {
			return $this->object_type;
		}

		return $this->object_subtype;
	}

	/**
	 * Gets the max number of pages available for the object type.
	 *
	 * @since 5.5.0
	 *
	 * @param string $object_subtype Optional. Object subtype. Default empty.
	 * @return int Total number of pages.
	 */
	public function max_num_pages( $object_subtype = '' ) {
		if ( empty( $object_subtype ) ) {
			$object_subtype = $this->get_queried_type();
		}

		$query = new WP_Query(
			array(
				'fields'                 => 'ids',
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'post_type'              => $object_subtype,
				'posts_per_page'         => core_sitemaps_get_max_urls( $this->object_type ),
				'paged'                  => 1,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			)
		);

		return isset( $query->max_num_pages ) ? $query->max_num_pages : 1;
	}

	/**
	 * Sets the object subtype.
	 *
	 * @since 5.5.0
	 *
	 * @param string $object_subtype The name of the object subtype.
	 * @return bool Returns true on success.
	 */
	public function set_object_subtype( $object_subtype ) {
		$this->object_subtype = $object_subtype;

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

		$object_subtypes = $this->get_object_subtypes();

		foreach ( $object_subtypes as $object_subtype ) {
			// Handle lists of post-objects.
			if ( isset( $object_subtype->name ) ) {
				$object_subtype = $object_subtype->name;
			}

			$sitemap_data[] = array(
				'name'   => $object_subtype,
				'pages' => $this->max_num_pages( $object_subtype ),
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
	 * Returns the list of supported object sub-types exposed by the provider.
	 *
	 * By default this is the subtype as specified in the class property.
	 *
	 * @since 5.5.0
	 *
	 * @return array List: containing object types or false if there are no subtypes.
	 */
	public function get_object_subtypes() {
		if ( ! empty( $this->object_subtype ) ) {
			return array( $this->object_subtype );
		}

		/**
		 * To prevent complexity in code calling this function, such as `get_sitemaps()` in this class,
		 * an iterable type is returned. The value false was chosen as it passes empty() checks and
		 * as semantically this provider does not provide sub-types.
		 *
		 * @link https://github.com/GoogleChromeLabs/wp-sitemaps/pull/72#discussion_r347496750
		 */
		return array( false );
	}
}
