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
}
