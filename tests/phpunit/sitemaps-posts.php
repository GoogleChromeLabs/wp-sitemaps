<?php

class Test_WP_Sitemaps_Posts extends WP_UnitTestCase {
	/**
	 * Test ability to filter object subtypes.
	 */
	public function test_filter_sitemaps_post_types() {
		$posts_provider = new WP_Sitemaps_Posts();

		// Return an empty array to show that the list of subtypes is filterable.
		add_filter( 'wp_sitemaps_post_types', '__return_empty_array' );
		$subtypes = $posts_provider->get_object_subtypes();

		$this->assertEquals( array(), $subtypes, 'Could not filter posts subtypes.' );
	}

	/**
	 * Test `wp_sitemaps_posts_show_on_front_entry` filter.
	 */
	public function test_posts_show_on_front_entry() {
		$posts_provider = new WP_Sitemaps_Posts();
		update_option( 'show_on_front', 'page' );

		add_filter( 'wp_sitemaps_posts_show_on_front_entry', array( $this, '_show_on_front_entry' ) );

		$url_list = $posts_provider->get_url_list( 1, 'page' );

		$this->assertEquals( array(), $url_list );

		update_option( 'show_on_front', 'posts' );

		$url_list      = $posts_provider->get_url_list( 1, 'page' );
		$sitemap_entry = array_shift( $url_list );

		$this->assertTrue( isset( $sitemap_entry['lastmod'] ) );
	}

	/**
	 * Callback for 'wp_sitemaps_posts_show_on_front_entry' filter.
	 */
	public function _show_on_front_entry( $sitemap_entry ) {
		$sitemap_entry['lastmod'] = wp_date( DATE_W3C, time() );

		return $sitemap_entry;
	}
}
