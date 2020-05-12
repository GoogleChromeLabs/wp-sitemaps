<?php
/**
 * Test sitemap provider.
 *
 * @package Core_Sitemaps
 */

/**
 * Class Sitemaps_Test_Provider.
 *
 * Provides test data for additional registered providers.
 */
class Sitemaps_Test_Provider extends Sitemaps_Provider {
	/**
	 * Sitemaps_Posts constructor.
	 *
	 * @param string $object_type Optional. Object type name to use. Default 'test'.
	 */
	public function __construct( $object_type = 'test' ) {
		$this->object_type = $object_type;
	}

	/**
	 * Return the public post types, which excludes nav_items and similar types.
	 * Attachments are also excluded. This includes custom post types with public = true
	 *
	 * @return array Map of object subtype objects keyed by their name.
	 */
	public function get_object_subtypes() {
		return array(
			'type-1' => (object) array( 'name' => 'type-1' ),
			'type-2' => (object) array( 'name' => 'type-2' ),
			'type-3' => (object) array( 'name' => 'type-3' ),
		);
	}

	/**
	 * Gets a URL list for a sitemap.
	 *
	 * @param int    $page_num       Page of results.
	 * @param string $object_subtype Optional. Object subtype name. Default empty.
	 * @return array List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num, $object_subtype = '' ) {
		return array();
	}

	/**
	 * Query for determining the number of pages.
	 *
	 * @param string $object_subtype Optional. Object subtype. Default empty.
	 * @return int Total number of pages.
	 */
	public function max_num_pages( $object_subtype = '' ) {
		return 4;
	}
}
