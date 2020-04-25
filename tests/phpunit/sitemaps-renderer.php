<?php

/**
 * @group renderer
 */
class Test_Core_Sitemaps_Renderer extends WP_UnitTestCase {
	public function tearDown() {
		// remove the tmp stylesheet file created by test_stylesheet_html().
		@unlink( get_temp_dir() . '/wp-sitemap.xsl' );
	}

	public function test_get_sitemap_stylesheet_url() {
		$sitemap_renderer = new Core_Sitemaps_Renderer();
		$stylesheet_url   = $sitemap_renderer->get_sitemap_stylesheet_url();

		$this->assertStringEndsWith( '/?sitemap-stylesheet=xsl', $stylesheet_url );
	}

	public function test_get_sitemap_stylesheet_url_pretty_permalinks() {
		// Set permalinks for testing.
		$this->set_permalink_structure( '/%year%/%postname%/' );

		$sitemap_renderer = new Core_Sitemaps_Renderer();
		$stylesheet_url   = $sitemap_renderer->get_sitemap_stylesheet_url();

		// Clean up permalinks.
		$this->set_permalink_structure();

		$this->assertStringEndsWith( '/wp-sitemap.xsl', $stylesheet_url );
	}

	public function test_get_sitemap_index_stylesheet_url() {
		$sitemap_renderer = new Core_Sitemaps_Renderer();
		$stylesheet_url   = $sitemap_renderer->get_sitemap_index_stylesheet_url();

		$this->assertStringEndsWith( '/?sitemap-stylesheet=index', $stylesheet_url );
	}

	public function test_get_sitemap_index_stylesheet_url_pretty_permalinks() {
		// Set permalinks for testing.
		$this->set_permalink_structure( '/%year%/%postname%/' );

		$sitemap_renderer = new Core_Sitemaps_Renderer();
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
	 * Test XML output for the sitemap index renderer when stylesheet is disabled.
	 */
	public function test_get_sitemap_index_xml_without_stylsheet() {
		$entries = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-post-1.xml',
			),
		);

		add_filter( 'core_sitemaps_stylesheet_index_url', '__return_false' );

		$renderer = new Core_Sitemaps_Renderer();

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

		$renderer = new Core_Sitemaps_Renderer();

		$actual   = $renderer->get_sitemap_xml( $url_list );
		$expected = '<?xml version="1.0" encoding="UTF-8"?>' .
					'<?xml-stylesheet type="text/xsl" href="http://' . WP_TESTS_DOMAIN . '/?sitemap-stylesheet=xsl" ?>' .
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
	 * Test XML output for the sitemap page renderer when stylesheet is disabled.
	 */
	public function test_get_sitemap_xml_without_stylsheet() {
		$url_list = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-1',
			),
		);

		add_filter( 'core_sitemaps_stylesheet_url', '__return_false' );

		$renderer = new Core_Sitemaps_Renderer();

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

		$renderer = new Core_Sitemaps_Renderer();

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

	/**
	 * Test that the HTML rendered by the stylsheet has the correct columns and values.
	 *
	 * @param string[] $columns The columns to render. Default: the empty array.
	 *
	 * @dataProvider stylesheet_columns_provider
	 */
	public function test_stylesheet_html( $columns ) {
		if ( ! empty( $columns ) ) {
			// add the hook so the stylesheet has the custom columns.
			add_filter(
				'core_sitemaps_stylesheet_columns',
				function( $_columns ) use ( $columns ) {
					return $columns;
				}
			);
		} else {
			// use the default columns.
			$columns = array(
				'http://www.sitemaps.org/schemas/sitemap/0.9' => array(
					'loc' => esc_xml__( 'URL', 'core-sitemaps' ),
				),
			);
		}

		$url_list = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-1',
				// include insigificant whitespace.
				'string'  => 'value   this is a test    ',
				// inclue children in the sitemap that are not output by the stylesheet.
				'number'  => 100,
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-2',
				'string'  => 'another value',
				// inclue children in the sitemap that are not output by the stylesheet.
				'number'  => 200,
			),
		);

		$renderer   = new Core_Sitemaps_Renderer();
		$xml_dom    = $this->loadXML( $renderer->get_sitemap_xml( $url_list ) );

		// We have to load the stylesheet from a file instead of a string given the use of document('') in
		// the stylesheet.   {@link https://www.w3.org/TR/1999/REC-xslt-19991116#function-document document()}
		// uses the {@link https://www.w3.org/TR/1999/REC-xslt-19991116#base-uri Base_URI}
		// of the stylesheet document node, and when loaded from a string the Base_URI is not set
		// such that that resolution can happen.
		$xslt_file  = get_temp_dir() . '/wp-sitemap.xsl';
		$stylesheet = new Core_Sitemaps_Stylesheet();
		file_put_contents( $xslt_file, $stylesheet->get_sitemap_stylesheet() );

		$xslt_dom   = $this->loadXML( $xslt_file );
		$xslt       = new XSLTProcessor();
		$this->assertTrue( $xslt->importStylesheet( $xslt_dom ) );

		// apply the stylesheet XSLT to the sitemap XML to generate the HTML.
		$html_dom   = $xslt->transformToDoc( $xml_dom );

		$xpath      = new DOMXPath( $html_dom );
		// get the table, to simplifiy the XPath expressions below.
		$table      = $xpath->query( '//table[@id = "sitemap__table"]' )->item( 0 );

		$this->assertEquals(
			1,
			$xpath->evaluate( 'count( thead/tr )', $table ),
			'Number of html table header rows incorrect.'
		);

		$header_idx = 0;
		foreach ( $columns as $namespace_uri => $namespace_columns ) {
			foreach ( $namespace_columns as $local_name => $header_text ) {
				$header_idx++;

				$this->assertEquals(
					preg_replace( '/\s+/', ' ', trim( $header_text ) ),
					$xpath->evaluate( "normalize-space( thead/tr/th[ {$header_idx} ] )", $table ),
					sprintf( 'Header text for Q{%s}%s incorrect.', $namespace_uri, $local_name )
				);

				foreach ( $url_list as $idx => $url ) {
					// XPath position() is 1-indexed, so incrememnt $idx accordingly.
					$idx++;

					if ( 'http://www.sitemaps.org/schemas/sitemap/0.9' === $namespace_uri && 'loc' === $local_name ) {
						$this->assertEquals(
							preg_replace( '/\s+/', ' ', trim( $url['loc'] ) ),
							$xpath->evaluate( "normalize-space( tbody/tr[ {$idx} ]/td[ {$header_idx} ]/a/@href )", $table ),
							'a/@href incorrect.'
						);
						$this->assertEquals(
							preg_replace( '/\s+/', ' ', trim( $url['loc'] ) ),
							$xpath->evaluate( "normalize-space( tbody/tr[ {$idx} ]/td[ {$header_idx} ]/a )", $table ),
							'a/text() incorrect.'
						);
					} else {
						$this->assertEquals(
							// when $url[ $local_name ] is not set, the stylesheet should render an empty "td" element.
							isset( $url[ $local_name ] ) ? preg_replace( '/\s+/', ' ', trim( $url[ $local_name ] ) ) : '',
							$xpath->evaluate( "normalize-space( tbody/tr[ {$idx} ]/td[ {$header_idx} ] )", $table ),
							sprintf( 'Table cell text for Q{%s}%s not correct.', $namespace_uri, $local_name )
						);
					}
				}
			}
		}

		// now that we know how many columns there should be,
		// check that there are no extra columns in either the table header or body.
		$this->assertEquals(
			$header_idx,
			$xpath->evaluate( 'count( thead/tr[1]/th )', $table ),
			'Number of html table header cells incorrect.'
		);
		foreach ( $url_list as $idx => $url ) {
			// XPath position() is 1-indexed, so incrememnt $idx accordingly.
			$idx++;

			$this->assertEquals(
				$header_idx,
				$xpath->evaluate( "count( tbody/tr[ {$idx} ]/td )", $table ),
				'Number of html table body cells incorrect.'
			);
		}
	}

	/**
	 * Data Provider for test_stylesheet_html().
	 *
	 * When this returns other than an empty array, the array will be returned
	 * from the `core_sitemaps_custom_columns` filter.
	 *
	 * @return string[]
	 */
	public function stylesheet_columns_provider() {
		return array(
			'default columns'       => array( array() ),
			'rename URL'            => array(
				array(
					'http://www.sitemaps.org/schemas/sitemap/0.9' => array(
						// use a different string for the header text for the loc column.
						'loc' => 'Permalink',
					),
				),
			),
			'XML escaped text'     => array(
				array(
					'http://www.sitemaps.org/schemas/sitemap/0.9' => array(
						// use a different string for the header text for the loc column.
						// also indirectly tests that esc_xml() ensures that the use
						// of HTML named character references doesn't result in
						// non-well-formed XML (in the absence of having full unit tests for esc_xml()).
						'loc' => esc_xml( 'This is &hellip; a test' ),
					),
				),
			),
			'add a column'          => array(
				array(
					'http://www.sitemaps.org/schemas/sitemap/0.9' => array(
						'loc'    => 'URL',
						// add a new column.
						'string' => 'String',
					),
				),
			),
			'URL last'              => array(
				array(
					'http://www.sitemaps.org/schemas/sitemap/0.9' => array(
						// add a new column before the URL column.
						'string' => 'String',
						'loc'    => 'URL',
					),
				),
			),
			'column not in sitemap' => array(
				array(
					'http://www.sitemaps.org/schemas/sitemap/0.9' => array(
						'loc'    => 'URL',
						// since there is no 'not' child in the sitemap,
						// this column should result in an empty "td" element in the table body.
						'not'    => 'Not in sitemap',
					),
				),
			),
		);
	}

	/**
	 * Load XML from a string.
	 *
	 * @param string $xml
	 * @param int    $options Bitwise OR of the {@link https://www.php.net/manual/en/libxml.constants.php libxml option constants}.
	 *                        Default is 0.
	 * @return DOMDocument
	 */
	public function loadXML( $xml, $options = 0 ) {
		// Suppress PHP warnings generated by DOMDocument::loadXML(), which would cause
		// PHPUnit to incorrectly report an error instead of a just a failure.
		$internal = libxml_use_internal_errors( true );
		libxml_clear_errors();

		$xml_dom = new DOMDocument();

		if ( is_file( $xml ) ) {
			$this->assertTrue(
				$xml_dom->load( $xml, $options ),
				libxml_get_last_error() ? sprintf( 'Non-well-formed XML: %s.', libxml_get_last_error()->message ) : ''
			);
		} else {
			$this->assertTrue(
				$xml_dom->loadXML( $xml, $options ),
				libxml_get_last_error() ? sprintf( 'Non-well-formed XML: %s.', libxml_get_last_error()->message ) : ''
			);
		}

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
