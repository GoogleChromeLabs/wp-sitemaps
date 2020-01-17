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
	 */
	public function __construct() {
		$this->object_type = 'test';
		$this->route       = '^sitemap-test-([A-z]+)-?([0-9]+)?\.xml$';
		$this->slug        = 'test';
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

}
