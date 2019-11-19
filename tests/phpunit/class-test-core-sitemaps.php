<?php
/**
 * Class Core_Sitemap_Tests
 *
 * @package   Core_Sitemaps
 * @copyright 2019 The Core Sitemaps Contributors
 * @license   GNU General Public License, version 2
 * @link      https://github.com/GoogleChromeLabs/wp-sitemaps
 */

use WP_UnitTestCase;

/**
 * Core sitemaps test cases.
 *
 * @group sitemaps
 */
class Core_Sitemaps_Tests extends WP_UnitTestCase {
	/**
	 * A single example test.
	 */
	public function test_sample() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}

	/**
	 * Test getting the correct number of URLs for a sitemap.
	 */
	public function test_core_sitemaps_get_max_urls() {
		// Apply a filter to test filterable values.
		add_filter( 'core_sitemaps_max_urls', array( $this, 'filter_max_url_value' ), 10, 2 );

		$this->assertEquals( core_sitemaps_get_max_urls(), CORE_SITEMAPS_MAX_URLS, 'Can not confirm max URL number.' );
		$this->assertEquals( core_sitemaps_get_max_urls( 'posts' ), 300, 'Can not confirm max URL number for posts.' );
		$this->assertEquals( core_sitemaps_get_max_urls( 'taxonomies'), 50, 'Can not confirm max URL number for taxonomies.' );
		$this->assertEquals( core_sitemaps_get_max_urls( 'users' ), 1, 'Can not confirm max URL number for users.' );

		// Clean up.
		remove_filter( 'core_sitemaps_max_urls', array( $this, 'filter_max_url_value' ) );
	}

	/**
	 * Callback function for testing the `core_sitemaps_max_urls` filter.
	 *
	 * This filter is documented in core-sitemaps/inc/functions.php.
	 *
	 * @see core_sitemaps_get_max_urls().
	 */
	public function filter_max_url_value( $max_urls, $type ) {
		switch ( $type ) {
			case 'posts':
				return 300;
			case 'taxonomies':
				return 50;
			case 'users':
				return 1;
			default:
				return $max_urls;
		}
	}
}
