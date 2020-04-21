<?php

class Test_Core_Sitemaps_Renderer extends WP_UnitTestCase {
	public function test_get_sitemap_stylesheet_url() {
		$sitemap_renderer = new Core_Sitemaps_Renderer();
		$stylesheet_url = $sitemap_renderer->get_sitemap_stylesheet_url();

		$this->assertStringEndsWith( '/?sitemap-stylesheet=xsl', $stylesheet_url );
	}

	public function test_get_sitemap_stylesheet_url_pretty_permalinks() {
		// Set permalinks for testing.
		$this->set_permalink_structure( '/%year%/%postname%/' );

		$sitemap_renderer = new Core_Sitemaps_Renderer();
		$stylesheet_url = $sitemap_renderer->get_sitemap_stylesheet_url();

		// Clean up permalinks.
		$this->set_permalink_structure();

		$this->assertStringEndsWith( '/wp-sitemap.xsl', $stylesheet_url );
	}

	public function test_get_sitemap_index_stylesheet_url() {
		$sitemap_renderer = new Core_Sitemaps_Renderer();
		$stylesheet_url = $sitemap_renderer->get_sitemap_index_stylesheet_url();

		$this->assertStringEndsWith( '/?sitemap-stylesheet=index', $stylesheet_url );
	}

	public function test_get_sitemap_index_stylesheet_url_pretty_permalinks() {
		// Set permalinks for testing.
		$this->set_permalink_structure( '/%year%/%postname%/' );

		$sitemap_renderer = new Core_Sitemaps_Renderer();
		$stylesheet_url = $sitemap_renderer->get_sitemap_index_stylesheet_url();

		// Clean up permalinks.
		$this->set_permalink_structure();

		$this->assertStringEndsWith( '/wp-sitemap-index.xsl', $stylesheet_url );
	}

	/**
	 * Test XML output for the sitemap index renderer.
	 */
	public function test_get_sitemap_index_xml() {
		$entries = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-post-1.xml',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-page-1.xml',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-category-1.xml',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-post_tag-1.xml',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-users-1.xml',
			),
		);

		$renderer = new Core_Sitemaps_Renderer();

		$xml = $renderer->get_sitemap_index_xml( $entries );

		$expected = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL .
					'<?xml-stylesheet type="text/xsl" href="http://' . WP_TESTS_DOMAIN . '/?sitemap-stylesheet=index" ?>' . PHP_EOL .
					'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
					'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-post-1.xml</loc></sitemap>' .
					'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-page-1.xml</loc></sitemap>' .
					'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-category-1.xml</loc></sitemap>' .
					'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-post_tag-1.xml</loc></sitemap>' .
					'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-users-1.xml</loc></sitemap>' .
					'</sitemapindex>' . PHP_EOL;

		$this->assertSame( $expected, $xml, 'Sitemap index markup incorrect.' );
	}

	/**
	 * Test XML output for the sitemap page renderer.
	 */
	public function test_get_sitemap_xml() {
		$url_list = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-1',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-2',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-3',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-4',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-5',
			),
		);

		$renderer = new Core_Sitemaps_Renderer();

		$xml = $renderer->get_sitemap_xml( $url_list );

		$expected = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL .
					'<?xml-stylesheet type="text/xsl" href="http://' . WP_TESTS_DOMAIN . '/?sitemap-stylesheet=xsl" ?>' . PHP_EOL .
					'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
					'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-1</loc></url>' .
					'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-2</loc></url>' .
					'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-3</loc></url>' .
					'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-4</loc></url>' .
					'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-5</loc></url>' .
					'</urlset>' . PHP_EOL;

		$this->assertSame( $expected, $xml, 'Sitemap page markup incorrect.' );
	}

	/**
	 * Ensure extra attributes added to URL lists are included in rendered XML.
	 */
	public function test_get_sitemap_xml_extra_attributes() {
		$url_list = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-1',
				'string'  => 'value',
				'number'  => 200,
			),
		);

		$renderer = new Core_Sitemaps_Renderer();

		$xml = $renderer->get_sitemap_xml( $url_list );

		$this->assertContains( '<string>value</string>', $xml, 'Extra string attributes are not being rendered in XML.' );
		$this->assertContains( '<number>200</number>', $xml, 'Extra number attributes are not being rendered in XML.' );
	}
}
