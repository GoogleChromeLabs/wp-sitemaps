<?php

class Test_Core_Sitemaps_Taxonomies extends WP_UnitTestCase {
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
	 * Editor ID for use in some tests.
	 *
	 * @var int
	 */
	public static $editor_id;

	/**
	 * Set up fixtures.
	 *
	 * @param WP_UnitTest_Factory $factory A WP_UnitTest_Factory object.
	 */
	public static function wpSetUpBeforeClass( $factory ) {
		self::$cats      = $factory->term->create_many( 10, array( 'taxonomy'  => 'category' ) );
		self::$post_tags = $factory->term->create_many( 10 );
		self::$editor_id  = $factory->user->create( array( 'role' => 'editor' ) );
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
			static function ( $id ) use ( $post ) {
				return array(
					'loc'     => get_term_link( $id, 'category' ),
				);
			},
			$categories
		);

		$this->assertSame( $expected_cats, $cat_list, 'Category URL list does not match.' );

		$tag_list = $tax_provider->get_url_list( 1, 'post_tag' );

		$expected_tags = array_map(
			static function ( $id ) use ( $post ) {
				return array(
					'loc'     => get_term_link( $id, 'post_tag' ),
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
			static function ( $id ) use ( $taxonomy, $post ) {
				return array(
					'loc'     => get_term_link( $id, $taxonomy ),
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

		$tax_provider = new Core_Sitemaps_Taxonomies();
		$entries = wp_list_pluck( $tax_provider->get_sitemap_entries(), 'loc' );

		// Clean up.
		unregister_taxonomy_for_object_type( 'public_taxonomy', 'post' );
		unregister_taxonomy_for_object_type( 'private_taxonomy', 'post' );

		$this->assertContains( 'http://' . WP_TESTS_DOMAIN . '/?sitemap=taxonomies&sitemap-sub-type=public_taxonomy&paged=1', $entries, 'Public Taxonomies are not in the index.' );
		$this->assertNotContains( 'http://' . WP_TESTS_DOMAIN . '/?sitemap=taxonomies&sitemap-sub-type=private_taxonomy&paged=1', $entries, 'Private Taxonomies are visible in the index.' );
	}

	/**
	 * Test ability to filter object subtypes.
	 */
	public function test_filter_core_sitemaps_taxonomies() {
		$taxonomies_provider = new Core_Sitemaps_Taxonomies();

		// Return an empty array to show that the list of subtypes is filterable.
		add_filter( 'core_sitemaps_taxonomies', '__return_empty_array' );
		$subtypes = $taxonomies_provider->get_object_subtypes();

		$this->assertEquals( array(), $subtypes, 'Could not filter taxonomies subtypes.' );
	}

	/**
	 * Test ability to filter the taxonomies URL list.
	 */
	public function test_filter_core_sitemaps_taxonomies_url_list() {
		$taxonomies_provider = new Core_Sitemaps_Taxonomies();

		add_filter( 'core_sitemaps_taxonomies_url_list', '__return_empty_array' );

		// Register taxonomy, create a term for it and assign a post to it.
		register_taxonomy( 'test_tax', 'post' );
		$term = self::factory()->term->create( array( 'taxonomy'  => 'test_tax' ) );
		$post = self::factory()->post->create();
		wp_set_post_terms( $post, array( $term ), 'test_tax' );

		$test_tax_url_list = $taxonomies_provider->get_url_list( 1, 'test_tax' );
		$this->assertEquals( array(), $test_tax_url_list, 'Could not filter taxonomies URL list.' );
	}
}
