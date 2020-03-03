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
use Core_Sitemaps_Test_Provider;

require_once( __DIR__ . '/inc/class-core-sitemaps-test-provider.php' );

/**
 * Core sitemaps test cases.
 *
 * @group sitemaps
 */
class Core_Sitemaps_Tests extends WP_UnitTestCase {

	/**
	 * List of user IDs.
	 *
	 * @var array
	 */
	public static $users;

	/**
	 * List of post_tag IDs.
	 *
	 * @var array
	 */
	public static $post_tags;

	/**
	 * List of category IDs.
	 *
	 * @var array
	 */
	public static $cats;

	/**
	 * List of post type post IDs.
	 *
	 * @var array
	 */
	public static $posts;

	/**
	 * List of post type page IDs.
	 *
	 * @var array
	 */
	public static $pages;

	/**
	 * Editor ID for use in some tests.
	 *
	 * @var int
	 */
	public static $editor_id;

	/**
	 * Test sitemap provider.
	 *
	 * @var Core_Sitemaps_Test_Provider
	 */
	public static $test_provider;

	/**
	 * Set up fixtures.
	 *
	 * @param WP_UnitTest_Factory $factory A WP_UnitTest_Factory object.
	 */
	public static function wpSetUpBeforeClass( $factory ) {
		self::$users     = $factory->user->create_many( 10 );
		self::$post_tags = $factory->term->create_many( 10 );
		self::$cats      = $factory->term->create_many( 10, array( 'taxonomy'  => 'category' ) );
		self::$pages     = $factory->post->create_many( 10, array( 'post_type' => 'page' ) );

		// Create a set of posts pre-assigned to tags and authors.
		self::$posts = $factory->post->create_many(
			10,
			array(
				'tags_input' => self::$post_tags,
				'post_author' => reset( self::$users ),
			)
		);

		// Create a user with an editor role to complete some tests.
		self::$editor_id  = $factory->user->create( array( 'role' => 'editor' ) );

		self::$test_provider = new Core_Sitemaps_Test_Provider();
	}

	public function test_private_site() {
		// Simulate private site (search engines discouraged).
		update_option( 'blog_public', '0' );

		$robots_text = apply_filters( 'robots_txt', '', true );

		// Simulate public site.
		update_option( 'blog_public', '1' );

		$this->assertFalse( wp_next_scheduled( 'core_sitemaps_calculate_lastmod' ) );
		$this->assertFalse( wp_next_scheduled( 'core_sitemaps_update_lastmod_taxonomies' ) );
		$this->assertFalse( wp_next_scheduled( 'core_sitemaps_update_lastmod_posts' ) );
		$this->assertFalse( wp_next_scheduled( 'core_sitemaps_update_lastmod_users' ) );
		$this->assertEmpty( $robots_text );
	}

	/**
	 * Test getting the correct number of URLs for a sitemap.
	 */
	public function test_core_sitemaps_get_max_urls() {
		// Apply a filter to test filterable values.
		add_filter( 'core_sitemaps_max_urls', array( $this, 'filter_max_url_value' ), 10, 2 );

		$expected_null = core_sitemaps_get_max_urls();
		$expected_posts = core_sitemaps_get_max_urls( 'posts' );
		$expected_taxonomies = core_sitemaps_get_max_urls( 'taxonomies' );
		$expected_users = core_sitemaps_get_max_urls( 'users' );

		// Clean up.
		remove_filter( 'core_sitemaps_max_urls', array( $this, 'filter_max_url_value' ) );

		$this->assertEquals( $expected_null, CORE_SITEMAPS_MAX_URLS, 'Can not confirm max URL number.' );
		$this->assertEquals( $expected_posts, 300, 'Can not confirm max URL number for posts.' );
		$this->assertEquals( $expected_taxonomies, 50, 'Can not confirm max URL number for taxonomies.' );
		$this->assertEquals( $expected_users, 1, 'Can not confirm max URL number for users.' );
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

	/**
	 * Test XML output for the sitemap page renderer.
	 */
	public function test_core_sitemaps_xml() {
		$url_list = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-1',
				'lastmod' => '2019-11-01T12:00:00+00:00',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-2',
				'lastmod' => '2019-11-01T12:00:10+00:00',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-3',
				'lastmod' => '2019-11-01T12:00:20+00:00',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-4',
				'lastmod' => '2019-11-01T12:00:30+00:00',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-5',
				'lastmod' => '2019-11-01T12:00:40+00:00',
			),
		);

		$renderer = new Core_Sitemaps_Renderer();

		$xml = $renderer->get_sitemap_xml( $url_list );

		$expected = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL .
		'<?xml-stylesheet type="text/xsl" href="http://' . WP_TESTS_DOMAIN . '/sitemap.xsl" ?>' . PHP_EOL .
		'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
		'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-1</loc><lastmod>2019-11-01T12:00:00+00:00</lastmod></url>' .
		'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-2</loc><lastmod>2019-11-01T12:00:10+00:00</lastmod></url>' .
		'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-3</loc><lastmod>2019-11-01T12:00:20+00:00</lastmod></url>' .
		'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-4</loc><lastmod>2019-11-01T12:00:30+00:00</lastmod></url>' .
		'<url><loc>http://' . WP_TESTS_DOMAIN . '/2019/10/post-5</loc><lastmod>2019-11-01T12:00:40+00:00</lastmod></url>' .
		'</urlset>' . PHP_EOL;

		$this->assertSame( $expected, $xml, 'Sitemap page markup incorrect.' );
	}

	/**
	 * Ensure extra attributes added to URL lists are included in rendered XML.
	 */
	public function test_core_sitemaps_xml_extra_atts() {
		$url_list = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/2019/10/post-1',
				'lastmod' => '2019-11-01T12:00:00+00:00',
				'string'  => 'value',
				'number'  => 200,
			),
		);

		$renderer = new Core_Sitemaps_Renderer();

		$xml = $renderer->get_sitemap_xml( $url_list );

		$this->assertContains( '<string>value</string>', $xml, 'Extra string attributes are not being rendered in XML.' );
		$this->assertContains( '<number>200</number>', $xml, 'Extra number attributes are not being rendered in XML.' );
	}

	/**
	 * Ensure URL lists can have attributes added via filters.
	 *
	 * @dataProvider _url_list_providers
	 *
	 * @param string $type     The object type to test.
	 * @param string $sub_type The object subtype to use when getting a URL list.
	 */
	public function test_add_attributes_to_url_list( $type, $sub_type ) {
		add_filter( 'core_sitemaps_' . $type . '_url_list', array( $this, '_add_attributes_to_url_list' ) );

		$providers = core_sitemaps_get_sitemaps();

		$post_list = $providers[ $type ]->get_url_list( 1, $sub_type );

		remove_filter( 'core_sitemaps_' . $type . '_url_list', array( $this, '_add_attributes_to_url_list' ) );

		foreach ( $post_list as $entry ) {
			$this->assertEquals( 'value', $entry['extra'], 'Could not add attributes to url lists for ' . $type . '.' );
		}
	}

	/**
	 * Data provider for `test_add_attributes_to_url_list()`.
	 *
	 * @return array A list of object types and sub types.
	 */
	public function _url_list_providers() {
		return array(
			array(
				'posts',
				'post',
			),
			array(
				'taxonomies',
				'post_tag',
			),
			array(
				'users',
				'',
			),
		);
	}

	/**
	 * Filter callback to add an extra value to URL lists.
	 *
	 * @param array $url_list A URL list from a sitemap provider.
	 * @return array The filtered URL list.
	 */
	public function _add_attributes_to_url_list( $url_list ) {
		$entries = array_map(
			function ( $entry ) {
				$entry['extra'] = 'value';

				return $entry;
			},
			$url_list
		);

		return $entries;
	}

	/**
	 * Test robots.txt output.
	 */
	public function test_robots_text() {
		// Get the text added to the default robots text output.
		$robots_text = apply_filters( 'robots_txt', '', true );
		$sitemap_string = 'Sitemap: http://' . WP_TESTS_DOMAIN . '/?sitemap=index';

		$this->assertNotFalse( strpos( $robots_text, $sitemap_string ), 'Sitemap URL not included in robots text.' );
	}

	/**
	 * Test robots.txt output with permalinks set.
	 */
	public function test_robots_text_with_permalinks() {
		// Set permalinks for testing.
		$this->set_permalink_structure( '/%year%/%postname%/' );

		// Get the text added to the default robots text output.
		$robots_text = apply_filters( 'robots_txt', '', true );
		$sitemap_string = 'Sitemap: http://' . WP_TESTS_DOMAIN . '/sitemap.xml';

		// Clean up permalinks.
		$this->set_permalink_structure();

		$this->assertNotFalse( strpos( $robots_text, $sitemap_string ), 'Sitemap URL not included in robots text.' );
	}

	/**
	 * Test robots.txt output with line feed prefix.
	 */
	public function test_robots_text_prefixed_with_line_feed() {
		// Get the text added to the default robots text output.
		$robots_text = apply_filters( 'robots_txt', '', true );
		$sitemap_string = "\nSitemap: ";

		$this->assertNotFalse( strpos( $robots_text, $sitemap_string ), 'Sitemap URL not prefixed with "\n".' );
	}

	/**
	 * Helper function to get all sitemap entries data.
	 *
	 * @return array A list of sitemap entires.
	 */
	public function _get_sitemap_entries() {
		$entries = array();

		$providers = core_sitemaps_get_sitemaps();

		foreach ( $providers as $provider ) {
			// Using `array_push` is more efficient than `array_merge` in the loop.
			array_push( $entries, ...$provider->get_sitemap_entries() );
		}

		return $entries;
	}

	/**
	 * Test default sitemap entries.
	 */
	public function test_get_sitemap_entries() {
		$entries = $this->_get_sitemap_entries();

		$expected = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/?sitemap=posts&sub_type=post&paged=1',
				'lastmod' => '',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/?sitemap=posts&sub_type=page&paged=1',
				'lastmod' => '',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/?sitemap=taxonomies&sub_type=category&paged=1',
				'lastmod' => '',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/?sitemap=taxonomies&sub_type=post_tag&paged=1',
				'lastmod' => '',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/?sitemap=users&paged=1',
				'lastmod' => '',
			),
		);

		$this->assertSame( $expected, $entries );
	}

	/**
	 * Test default sitemap entries with permalinks on.
	 */
	public function test_get_sitemap_entries_post_with_permalinks() {
		$this->set_permalink_structure( '/%year%/%postname%/' );

		$entries = $this->_get_sitemap_entries();

		$expected = array(
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/sitemap-posts-post-1.xml',
				'lastmod' => '',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/sitemap-posts-page-1.xml',
				'lastmod' => '',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/sitemap-taxonomies-category-1.xml',
				'lastmod' => '',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/sitemap-taxonomies-post_tag-1.xml',
				'lastmod' => '',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/sitemap-users-1.xml',
				'lastmod' => '',
			),
		);

		// Clean up permalinks.
		$this->set_permalink_structure();

		$this->assertSame( $expected, $entries );
	}

	/**
	 * Test sitemap index entries with public and private custom post types.
	 */
	public function test_get_sitemap_entries_custom_post_types() {
		// Register and create a public post type post.
		register_post_type( 'public_cpt', array( 'public' => true ) );
		self::factory()->post->create( array( 'post_type' => 'public_cpt' ) );

		// Register and create a private post type post.
		register_post_type( 'private_cpt', array( 'public' => false ) );
		self::factory()->post->create( array( 'post_type' => 'private_cpt' ) );

		$entries = wp_list_pluck( $this->_get_sitemap_entries(), 'loc' );

		// Clean up.
		unregister_post_type( 'public_cpt' );
		unregister_post_type( 'private_cpt' );

		$this->assertContains( 'http://' . WP_TESTS_DOMAIN . '/?sitemap=posts&sub_type=public_cpt&paged=1', $entries, 'Public CPTs are not in the index.' );
		$this->assertNotContains( 'http://' . WP_TESTS_DOMAIN . '/?sitemap=posts&sub_type=private_cpt&paged=1', $entries, 'Private CPTs are visible in the index.' );
	}

	/**
	 * Test sitemap index entries with public and private taxonomies.
	 */
	public function test_get_sitemap_entries_custom_taxonomies() {
		wp_set_current_user( self::$editor_id );

		// Create a custom public and private taxonomies for this test.
		register_taxonomy( 'public_taxonomy', 'post' );
		register_taxonomy( 'private_taxonomy', 'post', array( 'public' => false ) );

		// Create test terms in the custom taxonomy.
		$public_term  = self::factory()->term->create( array( 'taxonomy'  => 'public_taxonomy' ) );
		$private_term = self::factory()->term->create( array( 'taxonomy'  => 'private_taxonomy' ) );

		// Create a test post applied to all test terms.
		self::factory()->post->create_and_get(
			array(
				'tax_input' => array(
					'public_taxonomy'  => array( $public_term ),
					'private_taxonomy' => array( $private_term ),
				),
			)
		);

		$entries = wp_list_pluck( $this->_get_sitemap_entries(), 'loc' );

		// Clean up.
		unregister_taxonomy_for_object_type( 'public_taxonomy', 'post' );
		unregister_taxonomy_for_object_type( 'private_taxonomy', 'post' );

		$this->assertTrue( in_array( 'http://' . WP_TESTS_DOMAIN . '/?sitemap=taxonomies&sub_type=public_taxonomy&paged=1', $entries, true ), 'Public Taxonomies are not in the index.' );
		$this->assertFalse( in_array( 'http://' . WP_TESTS_DOMAIN . '/?sitemap=taxonomies&sub_type=private_taxonomy&paged=1', $entries, true ), 'Private Taxonomies are visible in the index.' );
	}

	/**
	 * Tests getting a URL list for post type post.
	 */
	public function test_get_url_list_post() {
		$providers = core_sitemaps_get_sitemaps();

		$post_list = $providers['posts']->get_url_list( 1, 'post' );

		$expected = $this->_get_expected_url_list( 'post', self::$posts );

		$this->assertEquals( $expected, $post_list );
	}

	/**
	 * Tests getting a URL list for post type page.
	 */
	public function test_get_url_list_page() {
		// Short circuit the show on front option.
		add_filter( 'pre_option_show_on_front', '__return_true' );

		$providers = core_sitemaps_get_sitemaps();

		$post_list = $providers['posts']->get_url_list( 1, 'page' );

		$expected = $this->_get_expected_url_list( 'page', self::$pages );

		// Clean up.
		remove_filter( 'pre_option_show_on_front', '__return_true' );

		$this->assertEquals( $expected, $post_list );
	}

	/**
	 * Tests getting a URL list for post type page with included home page.
	 */
	public function test_get_url_list_page_with_home() {
		// Create a new post to confirm the home page lastmod date.
		$new_post = self::factory()->post->create_and_get();

		$providers = core_sitemaps_get_sitemaps();

		$post_list = $providers['posts']->get_url_list( 1, 'page' );

		$expected = $this->_get_expected_url_list( 'page', self::$pages );

		// Add the homepage to the front of the URL list.
		array_unshift(
			$expected,
			array(
				'loc'     => home_url(),
				'lastmod' => mysql2date( DATE_W3C, $new_post->post_modified_gmt, false ),
			)
		);

		$this->assertEquals( $expected, $post_list );
	}

	/**
	 * Tests getting a URL list for a custom post type.
	 */
	public function test_get_url_list_cpt() {
		$post_type = 'custom_type';

		// Registered post types are private unless explicitly set to public.
		register_post_type( $post_type, array( 'public' => true ) );

		$ids = self::factory()->post->create_many( 10, array( 'post_type' => $post_type ) );

		$providers = core_sitemaps_get_sitemaps();

		$post_list = $providers['posts']->get_url_list( 1, $post_type );

		$expected = $this->_get_expected_url_list( $post_type, $ids );

		// Clean up.
		unregister_post_type( $post_type );

		$this->assertEquals( $expected, $post_list, 'Custom post type posts are not visible.' );
	}

	/**
	 * Tests getting a URL list for a private custom post type.
	 */
	public function test_get_url_list_cpt_private() {
		$post_type = 'private_type';

		// Create a private post type for testing against data leaking.
		register_post_type( $post_type, array( 'public' => false ) );

		self::factory()->post->create_many( 10, array( 'post_type' => $post_type ) );

		$providers = core_sitemaps_get_sitemaps();

		$post_list = $providers['posts']->get_url_list( 1, $post_type );

		// Clean up.
		unregister_post_type( $post_type );

		$this->assertEmpty( $post_list, 'Private post types may be returned by the post provider.' );
	}

	/**
	 * Test getting a URL list for default taxonomies via
	 * Core_Sitemaps_Taxonomies::get_url_list().
	 */
	public function test_get_url_list_taxonomies() {
		// Add the default category to the list of categories we're testing.
		$categories = array_merge( array( 1 ), self::$cats );

		// Create a test post to calculate update times.
		$post = self::factory()->post->create_and_get(
			array(
				'tags_input' => self::$post_tags,
				'post_category' => $categories,
			)
		);

		$tax_provider = new Core_Sitemaps_Taxonomies();

		$cat_list  = $tax_provider->get_url_list( 1, 'category' );

		$expected_cats = array_map(
			function ( $id ) use ( $post ) {
				return array(
					'loc'     => get_term_link( $id, 'category' ),
					'lastmod' => mysql2date( DATE_W3C, $post->post_modified_gmt, false ),
				);
			},
			$categories
		);

		$this->assertSame( $expected_cats, $cat_list, 'Category URL list does not match.' );

		$tag_list = $tax_provider->get_url_list( 1, 'post_tag' );

		$expected_tags = array_map(
			function ( $id ) use ( $post ) {
				return array(
					'loc'     => get_term_link( $id, 'post_tag' ),
					'lastmod' => mysql2date( DATE_W3C, $post->post_modified_gmt, false ),
				);
			},
			self::$post_tags
		);

		$this->assertSame( $expected_tags, $tag_list, 'Post Tags URL list does not match.' );
	}

	/**
	 * Test getting a URL list for a custom taxonomy via
	 * Core_Sitemaps_Taxonomies::get_url_list().
	 */
	public function test_get_url_list_custom_taxonomy() {
		wp_set_current_user( self::$editor_id );

		// Create a custom taxonomy for this test.
		$taxonomy = 'test_taxonomy';
		register_taxonomy( $taxonomy, 'post' );

		// Create test terms in the custom taxonomy.
		$terms = self::factory()->term->create_many( 10, array( 'taxonomy'  => $taxonomy ) );

		// Create a test post applied to all test terms.
		$post = self::factory()->post->create_and_get( array( 'tax_input' => array( $taxonomy => $terms ) ) );

		$expected = array_map(
			function ( $id ) use ( $taxonomy, $post ) {
				return array(
					'loc'     => get_term_link( $id, $taxonomy ),
					'lastmod' => mysql2date( DATE_W3C, $post->post_modified_gmt, false ),
				);
			},
			$terms
		);

		$tax_provider = new Core_Sitemaps_Taxonomies();

		$post_list = $tax_provider->get_url_list( 1, $taxonomy );

		// Clean up.
		unregister_taxonomy_for_object_type( $taxonomy, 'post' );

		$this->assertEquals( $expected, $post_list, 'Custom taxonomy term links are not visible.' );
	}

	/**
	 * Test getting a URL list for a private custom taxonomy via
	 * Core_Sitemaps_Taxonomies::get_url_list().
	 */
	public function test_get_url_list_custom_taxonomy_private() {
		wp_set_current_user( self::$editor_id );

		// Create a custom taxonomy for this test.
		$taxonomy = 'private_taxonomy';
		register_taxonomy( $taxonomy, 'post', array( 'public' => false ) );

		// Create test terms in the custom taxonomy.
		$terms = self::factory()->term->create_many( 10, array( 'taxonomy'  => $taxonomy ) );

		// Create a test post applied to all test terms.
		self::factory()->post->create( array( 'tax_input' => array( $taxonomy => $terms ) ) );

		$tax_provider = new Core_Sitemaps_Taxonomies();

		$post_list = $tax_provider->get_url_list( 1, $taxonomy );

		// Clean up.
		unregister_taxonomy_for_object_type( $taxonomy, 'post' );

		$this->assertEmpty( $post_list, 'Private taxonomy term links are visible.' );
	}

	/**
	 * Test getting a URL list for a users sitemap page via
	 * Core_Sitemaps_Users::get_url_list().
	 */
	public function test_get_url_list_users() {
		// Set up the user to an editor to assign posts to other users.
		wp_set_current_user( self::$editor_id );

		// Create a set of posts for each user and generate the expected URL list data.
		$expected = array_map(
			function ( $user_id ) {
				$post = self::factory()->post->create_and_get( array( 'post_author' => $user_id ) );

				return array(
					'loc'      => get_author_posts_url( $user_id ),
					'lastmod' => mysql2date( DATE_W3C, $post->post_modified_gmt, false ),
				);
			},
			self::$users
		);

		$user_provider = new Core_Sitemaps_Users();

		$url_list = $user_provider->get_url_list( 1 );

		$this->assertSame( $expected, $url_list );
	}

	/**
	 * Helper function for building an expected url list.
	 *
	 * @param string $type An object sub type, e.g., post type.
	 * @param array  $ids  An array of object IDs.
	 * @return array A formed URL list including 'loc' and 'lastmod' values.
	 */
	public function _get_expected_url_list( $type, $ids ) {
		$posts = get_posts(
			array(
				'include'   => $ids,
				'orderby'   => 'ID',
				'order'     => 'ASC',
				'post_type' => $type,
			)
		);

		return array_map(
			function ( $post ) {
				return array(
					'loc'     => get_permalink( $post ),
					'lastmod' => mysql2date( DATE_W3C, $post->post_modified_gmt, false ),
				);
			},
			$posts
		);
	}

	/**
	 * Test functionality that adds a new sitemap provider to the registry.
	 */
	public function test_register_sitemap_provider() {
		core_sitemaps_register_sitemap( 'test_sitemap', self::$test_provider );

		$sitemaps = core_sitemaps_get_sitemaps();

		$this->assertEquals( $sitemaps['test_sitemap'], self::$test_provider, 'Can not confirm sitemap registration is working.' );
	}
}
