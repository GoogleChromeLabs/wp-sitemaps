<?php

class Test_Core_Sitemaps_Index extends WP_UnitTestCase {
	public function test_get_index_url() {
		$sitemap_index = new Core_Sitemaps_Index();
		$index_url = $sitemap_index->get_index_url();

		$this->assertStringEndsWith( '/?sitemap=index', $index_url );
	}

	public function test_get_index_url_pretty_permalinks() {
		// Set permalinks for testing.
		$this->set_permalink_structure( '/%year%/%postname%/' );

		$sitemap_index = new Core_Sitemaps_Index();
		$index_url = $sitemap_index->get_index_url();

		// Clean up permalinks.
		$this->set_permalink_structure();

		$this->assertStringEndsWith( '/wp-sitemap.xml', $index_url );
	}
}
