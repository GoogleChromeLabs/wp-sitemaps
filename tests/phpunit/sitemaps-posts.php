<?php

class Test_Core_Sitemaps_Posts extends WP_UnitTestCase {
	/**
	 * Test ability to filter object subtypes.
	 */
	public function test_filter_core_sitemaps_post_types() {
		$posts_provider = new Core_Sitemaps_Posts();

		// Return an empty array to show that the list of subtypes is filterable.
		add_filter( 'core_sitemaps_post_types', '__return_empty_array' );
		$subtypes = $posts_provider->get_object_sub_types();

		$this->assertEquals( array(), $subtypes, 'Could not filter posts subtypes.' );
	}

	/**
	 * Test ability to filter the posts URL list.
	 */
	public function test_filter_core_sitemaps_posts_url_list() {
		$posts_provider = new Core_Sitemaps_Posts();

		add_filter( 'core_sitemaps_posts_url_list', '__return_empty_array' );
		// Use 'page' post type with 'show_on_front' set to 'posts' to ensure
		// this would not be empty without the filter.
		add_filter(
			'option_show_on_front',
			function() {
				return 'posts';
			}
		);
		$page_url_list = $posts_provider->get_url_list( 1, 'page' );

		$this->assertEquals( array(), $page_url_list, 'Could not filter posts URL list.' );
	}
}
