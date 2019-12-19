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
	 * Test getting the correct number of URLs for a sitemap.
	 */
	public function test_core_sitemaps_get_max_urls() {
		// Apply a filter to test filterable values.
		add_filter( 'core_sitemaps_max_urls', array( $this, 'filter_max_url_value' ), 10, 2 );

		$this->assertEquals( core_sitemaps_get_max_urls(), CORE_SITEMAPS_MAX_URLS, 'Can not confirm max URL number.' );
		$this->assertEquals( core_sitemaps_get_max_urls( 'posts' ), 300, 'Can not confirm max URL number for posts.' );
		$this->assertEquals( core_sitemaps_get_max_urls( 'taxonomies' ), 50, 'Can not confirm max URL number for taxonomies.' );
		$this->assertEquals( core_sitemaps_get_max_urls( 'users' ), 1, 'Can not confirm max URL number for users.' );

		// Clean up.
		remove_filter( 'core_sitemaps_max_urls', array( $this, 'filter_max_url_value' ) );
	}

	/**
	 * Callback function for testing the `core_sitemaps_max_urls` filter.
	 *
	 * @param int    $max_urls The maximum number of URLs included in a sitemap. Default 2000.
	 * @param string $type     Optional. The type of sitemap to be filtered. Default ''.
	 * @return int The maximum number of URLs.
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

	/**
	 * Test core_sitemaps_get_sitemaps default functionality
	 */
	public function test_core_sitemaps_get_sitemaps() {
		$sitemaps = core_sitemaps_get_sitemaps();

		$expected = array(
			'posts'      => 'Core_Sitemaps_Posts',
			'taxonomies' => 'Core_Sitemaps_Taxonomies',
			'users'      => 'Core_Sitemaps_Users',
		);

		$this->assertEquals( array_keys( $expected ), array_keys( $sitemaps ), 'Unable to confirm default sitemap types are registered.' );

		foreach ( $expected as $name => $provider ) {
			$this->assertTrue( is_a( $sitemaps[ $name ], $provider ), "Default $name sitemap is not a $provider object." );
		}
	}

	/**
	 * Test XML output for the sitemap index renderer.
	 */
	public function test_core_sitemaps_index_xml() {
		$entries = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/sitemap-posts-post-1.xml',
				'lastmod' => '2019-11-01T12:00:00+00:00',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/sitemap-posts-page-1.xml',
				'lastmod' => '2019-11-01T12:00:10+00:00',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/sitemap-taxonomies-category-1.xml',
				'lastmod' => '2019-11-01T12:00:20+00:00',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/sitemap-taxonomies-post_tag-1.xml',
				'lastmod' => '2019-11-01T12:00:30+00:00',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/sitemap-users-1.xml',
				'lastmod' => '2019-11-01T12:00:40+00:00',
			),
		);

		$renderer = new Core_Sitemaps_Renderer();

		$xml = $renderer->get_sitemap_index_xml( $entries );

		$expected = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL .
		'<?xml-stylesheet type="text/xsl" href="http://' . WP_TESTS_DOMAIN . '/sitemap-index.xsl" ?>' . PHP_EOL .
		'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
		'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/sitemap-posts-post-1.xml</loc><lastmod>2019-11-01T12:00:00+00:00</lastmod></sitemap>' .
		'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/sitemap-posts-page-1.xml</loc><lastmod>2019-11-01T12:00:10+00:00</lastmod></sitemap>' .
		'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/sitemap-taxonomies-category-1.xml</loc><lastmod>2019-11-01T12:00:20+00:00</lastmod></sitemap>' .
		'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/sitemap-taxonomies-post_tag-1.xml</loc><lastmod>2019-11-01T12:00:30+00:00</lastmod></sitemap>' .
		'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/sitemap-users-1.xml</loc><lastmod>2019-11-01T12:00:40+00:00</lastmod></sitemap>' .
		'</sitemapindex>' . PHP_EOL;

		$this->assertSame( $expected, $xml, 'Sitemap index markup incorrect.' );
	}
}
