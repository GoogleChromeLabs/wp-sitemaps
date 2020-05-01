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

	/**
	 * Test robots.txt output.
	 */
	public function test_robots_text() {
		// Get the text added to the default robots text output.
		$robots_text = apply_filters( 'robots_txt', '', true );
		$sitemap_string = 'Sitemap: http://' . WP_TESTS_DOMAIN . '/?sitemap=index';

		$this->assertContains( $sitemap_string, $robots_text, 'Sitemap URL not included in robots text.' );
	}

	/**
	 * Test robots.txt output for a private site.
	 */
	public function test_robots_text_private_site() {
		$robots_text = apply_filters( 'robots_txt', '', false );
		$sitemap_string = 'Sitemap: http://' . WP_TESTS_DOMAIN . '/?sitemap=index';

		$this->assertNotContains( $sitemap_string, $robots_text );
	}

	/**
	 * Test robots.txt output with permalinks set.
	 */
	public function test_robots_text_with_permalinks() {
		// Set permalinks for testing.
		$this->set_permalink_structure( '/%year%/%postname%/' );

		// Get the text added to the default robots text output.
		$robots_text = apply_filters( 'robots_txt', '', true );
		$sitemap_string = 'Sitemap: http://' . WP_TESTS_DOMAIN . '/wp-sitemap.xml';

		// Clean up permalinks.
		$this->set_permalink_structure();

		$this->assertContains( $sitemap_string, $robots_text, 'Sitemap URL not included in robots text.' );
	}

	/**
	 * Test robots.txt output with line feed prefix.
	 */
	public function test_robots_text_prefixed_with_line_feed() {
		// Get the text added to the default robots text output.
		$robots_text = apply_filters( 'robots_txt', '', true );
		$sitemap_string = "\nSitemap: ";

		$this->assertContains( $sitemap_string, $robots_text, 'Sitemap URL not prefixed with "\n".' );
	}
}
