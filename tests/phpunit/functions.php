<?php

class Test_Core_Sitemaps_Functions extends WP_UnitTestCase {
	/**
	 * Test getting the correct number of URLs for a sitemap.
	 */
	public function test_sitemaps_get_max_urls() {
		// Apply a filter to test filterable values.
		add_filter( 'sitemaps_max_urls', array( $this, '_filter_max_url_value' ), 10, 2 );

		$expected_posts = sitemaps_get_max_urls( 'post' );
		$expected_taxonomies = sitemaps_get_max_urls( 'term' );
		$expected_users = sitemaps_get_max_urls( 'user' );

		$this->assertEquals( $expected_posts, 300, 'Can not confirm max URL number for posts.' );
		$this->assertEquals( $expected_taxonomies, 50, 'Can not confirm max URL number for taxonomies.' );
		$this->assertEquals( $expected_users, 1, 'Can not confirm max URL number for users.' );
	}

	/**
	 * Callback function for testing the `sitemaps_max_urls` filter.
	 *
	 * @param int    $max_urls The maximum number of URLs included in a sitemap. Default 2000.
	 * @param string $type     Optional. The type of sitemap to be filtered. Default ''.
	 * @return int The maximum number of URLs.
	 */
	public function _filter_max_url_value( $max_urls, $type ) {
		switch ( $type ) {
			case 'post':
				return 300;
			case 'term':
				return 50;
			case 'user':
				return 1;
			default:
				return $max_urls;
		}
	}

	/**
	 * Test sitemaps_get_sitemaps default functionality
	 */
	public function test_sitemaps_get_sitemaps() {
		$sitemaps = sitemaps_get_sitemaps();

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
}
