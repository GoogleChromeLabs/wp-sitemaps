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
	}

	/**
	 * Test getting the correct number of URLs for a sitemap.
	 */
	public function test_core_sitemaps_get_max_urls() {
		// Apply a filter to test filterable values.
		add_filter( 'core_sitemaps_max_urls', array( $this, 'filter_max_url_value' ), 10, 2 );

		$this->assertEquals( core_sitemaps_get_max_urls(), CORE_SITEMAPS_MAX_URLS, 'Can not confirm max URL number.' );
		$this->assertEquals( core_sitemaps_get_max_urls( 'posts' ), 300, 'Can not confirm max URL number for posts.' );
		$this->assertEquals( core_sitemaps_get_max_urls( 'taxonomies' ), 50, 'Can not confirm max URL number for taxonomies.' );
		$this->assertEquals( core_sitemaps_get_max_urls( 'users' ), 1, 'Can not confirm max URL number for users.' );

		// Clean up.
		remove_filter( 'core_sitemaps_max_urls', array( $this, 'filter_max_url_value' ) );
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
	 * Helper function to get all sitemap entries data.
	 *
	 * @return array A list of sitemap entires.
	 */
	public function _get_sitemap_entries() {
		$entries   = array();

		$providers = core_sitemaps_get_sitemaps();

		foreach ( $providers as $provider ) {
			$entries = array_merge( $entries, $provider->get_sitemap_entries() );
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

		$this->assertSame( $expected, $entries );
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

		$this->assertEquals( $expected, $post_list );

		// Clean up.
		remove_filter( 'pre_option_show_on_front', '__return_true' );
	}

	/**
	 * Tests getting a URL list for post type page with included home page.
	 */
	public function test_get_url_list_page_with_home() {
		// Create a new post to confirm the home page lastmod date.
		$new_post = $this->factory->post->create_and_get();

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

		register_post_type( $post_type );

		$ids = $this->factory->post->create_many( 10, array( 'post_type' => $post_type ) );

		$providers = core_sitemaps_get_sitemaps();

		$post_list = $providers['posts']->get_url_list( 1, $post_type );

		$expected = $this->_get_expected_url_list( $post_type, $ids );

		$this->assertEquals( $expected, $post_list );

		// Clean up.
		unregister_post_type( $post_type );
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
}
