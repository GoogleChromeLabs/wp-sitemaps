<?php
/**
 * Sitemaps: Core_Sitemaps_Tests class
 *
 * Main test class.
 *
 * @package   Core_Sitemaps
 * @copyright 2019 The Core Sitemaps Contributors
 * @license   GNU General Public License, version 2
 * @link      https://github.com/GoogleChromeLabs/wp-sitemaps
 */

require_once( __DIR__ . '/inc/class-core-sitemaps-test-provider.php' );

/**
 * Core sitemaps test cases.
 *
 * @group sitemaps
 */
class Test_Core_Sitemaps extends WP_UnitTestCase {

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
			static function ( $entry ) {
				$entry['extra'] = 'value';

				return $entry;
			},
			$url_list
		);

		return $entries;
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
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-post-1.xml',
				'lastmod' => '',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-posts-page-1.xml',
				'lastmod' => '',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-category-1.xml',
				'lastmod' => '',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-taxonomies-post_tag-1.xml',
				'lastmod' => '',
			),
			array(
				'loc'     => 'http://' . WP_TESTS_DOMAIN . '/wp-sitemap-users-1.xml',
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
			static function ( $post ) {
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
