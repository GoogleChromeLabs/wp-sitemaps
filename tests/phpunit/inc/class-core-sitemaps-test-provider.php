<?php
/**
 * Test sitemap provider.
 *
 * @package Core_Sitemaps
 */

/**
 * Class Core_Sitemaps_Test_Provider.
 *
 * Provides test data for additional registered providers.
 */
class Core_Sitemaps_Test_Provider extends Core_Sitemaps_Provider {
	/**
	 * Core_Sitemaps_Posts constructor.
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
	 * @return array $post_types List of registered object sub types.
	 */
	public function get_object_sub_types() {
		return array( 'type-1', 'type-2', 'type-3' );
	}

	/**
	 * Query for determining the number of pages.
	 *
	 * @param string $type Optional. Object type. Default is null.
	 * @return int Total number of pages.
	 */
	public function max_num_pages( $type = '' ) {
		return 4;
	}
}
