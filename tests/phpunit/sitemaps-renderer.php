<?php

/**
 * @group renderer
 */
class Test_WP_Sitemaps_Renderer extends WP_UnitTestCase {
	public function test_get_sitemap_stylesheet_url() {
		$sitemap_renderer = new WP_Sitemaps_Renderer();
		$stylesheet_url   = $sitemap_renderer->get_sitemap_stylesheet_url();

		$this->assertStringEndsWith( '/?sitemap-stylesheet=sitemap', $stylesheet_url );
	}

	public function test_get_sitemap_stylesheet_url_pretty_permalinks() {
		// Set permalinks for testing.
		$this->set_permalink_structure( '/%year%/%postname%/' );

		$sitemap_renderer = new WP_Sitemaps_Renderer();
		$stylesheet_url   = $sitemap_renderer->get_sitemap_stylesheet_url();

		// Clean up permalinks.
		$this->set_permalink_structure();

		$this->assertStringEndsWith( '/wp-sitemap.xsl', $stylesheet_url );
	}

	public function test_get_sitemap_index_stylesheet_url() {
		$sitemap_renderer = new WP_Sitemaps_Renderer();
		$stylesheet_url   = $sitemap_renderer->get_sitemap_index_stylesheet_url();

		$this->assertStringEndsWith( '/?sitemap-stylesheet=index', $stylesheet_url );
	}

	public function test_get_sitemap_index_stylesheet_url_pretty_permalinks() {
		// Set permalinks for testing.
		$this->set_permalink_structure( '/%year%/%postname%/' );

		$sitemap_renderer = new WP_Sitemaps_Renderer();
		$stylesheet_url   = $sitemap_renderer->get_sitemap_index_stylesheet_url();

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
				'loc' => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-post-1.xml',
			),
			array(
				'loc' => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-page-1.xml',
			),
			array(
				'loc' => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-category-1.xml',
			),
			array(
				'loc' => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-post_tag-1.xml',
			),
			array(
				'loc' => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-users-1.xml',
			),
		);

		$renderer = new WP_Sitemaps_Renderer();

		$actual   = $renderer->get_sitemap_index_xml( $entries );
		$expected = '<?xml version="1.0" encoding="UTF-8"?>' .
					'<?xml-stylesheet type="text/xsl" href="http://' . WP_TESTS_DOMAIN . '/?sitemap-stylesheet=index" ?>' .
					'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
					'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-post-1.xml</loc></sitemap>' .
					'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-page-1.xml</loc></sitemap>' .
					'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-category-1.xml</loc></sitemap>' .
					'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-post_tag-1.xml</loc></sitemap>' .
					'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-users-1.xml</loc></sitemap>' .
					'</sitemapindex>';

		$this->assertXMLEquals( $expected, $actual, 'Sitemap index markup incorrect.' );
	}

	/**
	 * Test XML output for the sitemap index renderer with multiple attributes.
	 */
	public function test_get_sitemap_index_xml_with_multiple_attributes() {
		$entries = array(
			array(
				'loc' => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-post-1.xml',
				'lastmod' => '2005-01-01',
			),
			array(
				'loc' => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-page-1.xml',
				'lastmod' => '2005-01-01',
			),
			array(
				'loc' => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-category-1.xml',
				'lastmod' => '2005-01-01',
			),
			array(
				'loc' => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-post_tag-1.xml',
				'lastmod' => '2005-01-01',
			),
			array(
				'loc' => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-users-1.xml',
				'lastmod' => '2005-01-01',
			),
		);

		$renderer = new WP_Sitemaps_Renderer();

		$actual   = $renderer->get_sitemap_index_xml( $entries );
		$expected = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<?xml-stylesheet type="text/xsl" href="http://' . WP_TESTS_DOMAIN . '/?sitemap-stylesheet=index" ?>' .
			'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
			'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-post-1.xml</loc><lastmod>2005-01-01</lastmod></sitemap>' .
			'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-page-1.xml</loc><lastmod>2005-01-01</lastmod></sitemap>' .
			'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-category-1.xml</loc><lastmod>2005-01-01</lastmod></sitemap>' .
			'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-post_tag-1.xml</loc><lastmod>2005-01-01</lastmod></sitemap>' .
			'<sitemap><loc>http://' . WP_TESTS_DOMAIN . '/wp-sitemap-users-1.xml</loc><lastmod>2005-01-01</lastmod></sitemap>' .
			'</sitemapindex>';

		$this->assertXMLEquals( $expected, $actual, 'Sitemap index markup incorrect.' );
	}


	/**
	 * Test XML output for the sitemap index renderer when stylesheet is disabled.
	 */
	public function test_get_sitemap_index_xml_without_stylesheet() {
		$entries = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-post-1.xml',
			),
		);

		add_filter( 'wp_sitemaps_stylesheet_index_url', '__return_false' );

		$renderer = new WP_Sitemaps_Renderer();

		$xml_dom   = $this->loadXML( $renderer->get_sitemap_index_xml( $entries ) );
		$xpath    = new DOMXPath( $xml_dom );

		$this->assertSame(
			0,
			$xpath->query( '//processing-instruction( "xml-stylesheet" )' )->length,
			'Sitemap index incorrectly contains the xml-stylesheet processing instruction.'
		);
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

		$renderer = new WP_Sitemaps_Renderer();

		$actual   = $renderer->get_sitemap_xml( $url_list );
		$expected = '<?xml version="1.0" encoding="UTF-8"?>' .
					'<?xml-stylesheet type="text/xsl" href="http://' . WP_TESTS_DOMAIN . '/?sitemap-stylesheet=sitemap" ?>' .
					'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
					'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-1</loc></url>' .
					'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-2</loc></url>' .
					'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-3</loc></url>' .
					'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-4</loc></url>' .
					'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-5</loc></url>' .
					'</urlset>';

		$this->assertXMLEquals( $expected, $actual, 'Sitemap page markup incorrect.' );
	}

	/**
	 * Test XML output for the sitemap page renderer.
	 */
	public function test_get_sitemap_xml_with_multiple_attributes() {
		$url_list = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-1',
				'lastmod' => '2005-01-01',
				'image:image' => array(
					'image:loc' => 'http://example.com/image.jpg',
				),
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-2',
				'lastmod' => '2005-01-01',
				'image:image' => array(
					'image:loc' => 'http://example.com/image.jpg',
				),
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-3',
				'lastmod' => '2005-01-01',
				'image:image' => array(
					'image:loc' => 'http://example.com/image.jpg',
				),
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-4',
				'lastmod' => '2005-01-01',
				'image:image' => array(
					'image:loc' => 'http://example.com/image.jpg',
				),
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-5',
				'lastmod' => '2005-01-01',
				'image:image' => array(
					'image:loc' => 'http://example.com/image.jpg',
				),
			),
		);

		$renderer = new WP_Sitemaps_Renderer();

		$actual   = $renderer->get_sitemap_xml( $url_list );
		$expected = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<?xml-stylesheet type="text/xsl" href="http://' . WP_TESTS_DOMAIN . '/?sitemap-stylesheet=sitemap" ?>' .
			'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
			'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-1</loc><lastmod>2005-01-01</lastmod><image:image><image:loc>http://example.com/image.jpg</image:loc></image:image></url>' .
			'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-2</loc><lastmod>2005-01-01</lastmod><image:image><image:loc>http://example.com/image.jpg</image:loc></image:image></url>' .
			'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-3</loc><lastmod>2005-01-01</lastmod><image:image><image:loc>http://example.com/image.jpg</image:loc></image:image></url>' .
			'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-4</loc><lastmod>2005-01-01</lastmod><image:image><image:loc>http://example.com/image.jpg</image:loc></image:image></url>' .
			'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-5</loc><lastmod>2005-01-01</lastmod><image:image><image:loc>http://example.com/image.jpg</image:loc></image:image></url>' .
			'</urlset>';

		$this->assertXMLEquals( $expected, $actual, 'Sitemap page markup incorrect.' );
	}


	/**
	 * Test XML output for the sitemap page renderer when stylesheet is disabled.
	 */
	public function test_get_sitemap_xml_without_stylesheet() {
		$url_list = array(
			array(
				'loc'  => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-1',
			),
		);

		add_filter( 'wp_sitemaps_stylesheet_url', '__return_false' );

		$renderer = new WP_Sitemaps_Renderer();

		$xml_dom   = $this->loadXML( $renderer->get_sitemap_xml( $url_list ) );
		$xpath    = new DOMXPath( $xml_dom );

		$this->assertSame(
			0,
			$xpath->query( '//processing-instruction( "xml-stylesheet" )' )->length,
			'Sitemap incorrectly contains the xml-stylesheet processing instruction.'
		);
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
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-2',
				'string'  => 'another value',
				'number'  => 300,
			),
		);

		$renderer = new WP_Sitemaps_Renderer();

		$xml_dom   = $this->loadXML( $renderer->get_sitemap_xml( $url_list ) );
		$xpath    = new DOMXPath( $xml_dom );
		$xpath->registerNamespace( 'sitemap', 'http://www.sitemaps.org/schemas/sitemap/0.9' );

		$this->assertEquals(
			count( $url_list ),
			$xpath->evaluate( 'count( /sitemap:urlset/sitemap:url/sitemap:string )' ),
			'Extra string attributes are not being rendered in XML.'
		);
		$this->assertEquals(
			count( $url_list ),
			$xpath->evaluate( 'count( /sitemap:urlset/sitemap:url/sitemap:number )' ),
			'Extra number attributes are not being rendered in XML.'
		);

		foreach ( $url_list as $idx => $url_item ) {
			// XPath position() is 1-indexed, so incrememnt $idx accordingly.
			$idx++;

			$this->assertEquals(
				$url_item['string'],
				$xpath->evaluate( "string( /sitemap:urlset/sitemap:url[ {$idx} ]/sitemap:string )" ),
				'Extra string attributes are not being rendered in XML.'
			);
			$this->assertEquals(
				$url_item['number'],
				$xpath->evaluate( "string( /sitemap:urlset//sitemap:url[ {$idx} ]/sitemap:number )" ),
				'Extra number attributes are not being rendered in XML.'
			);
		}
	}

	public function test_filter_urlset_attribures() {
		add_filter( 'core_sitemaps_urlset_attributes', array( $this, '_filter_urlset_attributes' ) );

		$url_list = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-1',
				'lastmod' => '2005-01-01',
				'image:image' => array(
					'image:loc' => 'http://example.com/image.jpg',
				),
			),
		);

		$renderer = new WP_Sitemaps_Renderer();

		$actual   = $renderer->get_sitemap_xml( $url_list );
		$expected = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<?xml-stylesheet type="text/xsl" href="http://' . WP_TESTS_DOMAIN . '/?sitemap-stylesheet=sitemap" ?>' .
			'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' .
			'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-1</loc><lastmod>2005-01-01</lastmod><image:image><image:loc>http://example.com/image.jpg</image:loc></image:image></url>' .
			'</urlset>';

		remove_filter( 'core_sitemaps_urlset_attributes', array( $this, '_filter_urlset_attributes' ) );

		$this->assertXMLEquals( $expected, $actual, 'Sitemap page markup incorrect.' );
	}

	public function _filter_urlset_attributes( $attributes ) {
		$attributes['xmlns:image'] = 'http://www.google.com/schemas/sitemap-image/1.1';
		return $attributes;
	}

	/**
	 * Load XML from a string.
	 *
	 * @param string $xml
	 * @param int    $options Bitwise OR of the {@link https://www.php.net/manual/en/libxml.constants.php libxml option constants}.
	 *                        Default is 0.
	 * @return DOMDocument The DOMDocument object loaded from the XML.
	 */
	public function loadXML( $xml, $options = 0 ) {
		// Suppress PHP warnings generated by DOMDocument::loadXML(), which would cause
		// PHPUnit to incorrectly report an error instead of a just a failure.
		$internal = libxml_use_internal_errors( true );
		libxml_clear_errors();

		$xml_dom = new DOMDocument();

		$this->assertTrue(
			$xml_dom->loadXML( $xml, $options ),
			libxml_get_last_error() ? sprintf( 'Non-well-formed XML: %s.', libxml_get_last_error()->message ) : ''
		);

		// Restore default error handler.
		libxml_use_internal_errors( $internal );
		libxml_clear_errors();

		return $xml_dom;
	}

	/**
	 * Normalize an XML document to make comparing two documents easier.
	 *
	 * @param string $xml
	 * @param int    $options Bitwise OR of the {@link https://www.php.net/manual/en/libxml.constants.php libxml option constants}.
	 *                        Default is 0.
	 * @return string The normalized form of `$xml`.
	 */
	public function normalizeXML( $xml, $options = 0 ) {
		static $xslt_proc;

		if ( ! $xslt_proc ) {
			$xslt_proc = new XSLTProcessor();
			$xslt_proc->importStyleSheet( simplexml_load_file( WP_TESTS_ASSETS_DIR . '/normalize-xml.xsl' ) );
		}

		return $xslt_proc->transformToXML( $this->loadXML( $xml, $options ) );
	}

	/**
	 * Reports an error identified by `$message` if the namespace normalized form of the XML document in `$actualXml`
	 * is equal to the namespace normalized form of the XML document in `$expectedXml`.
	 *
	 * This is similar to {@link https://phpunit.de/manual/6.5/en/appendixes.assertions.html#appendixes.assertions.assertXmlStringEqualsXmlString assertXmlStringEqualsXmlString()}
	 * except that differences in namespace prefixes are normalized away, such that given
	 * `$actualXml = "<root xmlns='urn:wordpress.org'><child/></root>";` and
	 * `$expectedXml = "<ns0:root xmlns:ns0='urn:wordpress.org'><ns0:child></ns0:root>";`
	 * then `$this->assertXMLEquals( $expectedXml, $actualXml )` will succeed.
	 *
	 * @param string $expectedXml
	 * @param string $actualXml
	 * @param string $message   Optional. Message to display when the assertion fails.
	 */
	public function assertXMLEquals( $expectedXml, $actualXml, $message = '' ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$this->assertEquals( $this->normalizeXML( $expectedXml ), $this->normalizeXML( $actualXml ), $message ); //phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	}

	/**
	 * Reports an error identified by `$message` if the namespace normalized form of the XML document in `$actualXml`
	 * is not equal to the namespace normalized form of the XML document in `$expectedXml`.
	 *
	 * This is similar to {@link https://phpunit.de/manual/6.5/en/appendixes.assertions.html#appendixes.assertions.assertXmlStringEqualsXmlString assertXmlStringNotEqualsXmlString()}
	 * except that differences in namespace prefixes are normalized away, such that given
	 * `$actualXml = "<root xmlns='urn:wordpress.org'><child></root>";` and
	 * `$expectedXml = "<ns0:root xmlns:ns0='urn:wordpress.org'><ns0:child/></ns0:root>";`
	 * then `$this->assertXMLNotEquals( $expectedXml, $actualXml )` will fail.
	 *
	 * @param string $expectedXml
	 * @param string $actualXml
	 * @param string $message   Optional. Message to display when the assertion fails.
	 */
	public function assertXMLNotEquals( $expectedXml, $actualXml, $message = '' ) { //phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$this->assertNotEquals( $this->normalizeXML( $expectedXml ), $this->normalizeXML( $actualXml ), $message ); //phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	}
}
