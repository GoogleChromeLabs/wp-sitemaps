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
abstract class Core_Sitemaps_Provider {

	/**
	 * Provider name.
	 *
	 * This will also be used as the public-facing name in URLs.
	 *
	 * @since 5.5.0
	 *
	 * @var string
	 */
	protected $name = '';

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
	 * @return array List of URLs for a sitemap.
	 */
	abstract public function get_url_list( $page_num, $object_subtype = '' );

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
	abstract public function max_num_pages( $object_subtype = '' );

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

		foreach ( $object_subtypes as $object_subtype_name => $data ) {
			$sitemap_data[] = array(
				'name'   => $object_subtype_name,
				'pages' => $this->max_num_pages( $object_subtype_name ),
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

		if ( ! $wp_rewrite->using_permalinks() ) {
			return add_query_arg(
				// Accounts for cases where name is not included, ex: sitemaps-users-1.xml.
				array_filter(
					array(
						'sitemap'          => $this->name,
						'sitemap-sub-type' => $name,
						'paged'            => $page,
					)
				),
				home_url( '/' )
			);
		}

		$basename = sprintf(
			'/wp-sitemap-%1$s.xml',
			implode(
				'-',
				// Accounts for cases where name is not included, ex: sitemaps-users-1.xml.
				array_filter(
					array(
						$this->name,
						$name,
						(string) $page,
					)
				)
			)
		);

		return home_url( $basename );
	}

	/**
	 * Returns the list of supported object sub-types exposed by the provider.
	 *
	 * @since 5.5.0
	 *
	 * @return array List of object subtypes objects keyed by their name.
	 */
	public function get_object_subtypes() {
		if ( ! empty( $this->object_subtype ) ) {
			return array(
				$this->object_subtype => (object) array( 'name' => $this->object_subtype ),
			);
		}

		/**
		 * To prevent complexity in code calling this function, such as `get_sitemap_type_data()`
		 * in this class, a non-empty array is returned, so that sitemaps for providers without
		 * object subtypes are still registered correctly.
		 *
		 * @link https://github.com/GoogleChromeLabs/wp-sitemaps/pull/72#discussion_r347496750
		 */
		return array(
			'' => (object) array( 'name' => '' ),
		);
	}
}
