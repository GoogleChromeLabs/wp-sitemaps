<?php

/**
 * Class Core_Sitemaps_Posts.
 * Builds the sitemap pages for Posts.
 */
class Core_Sitemaps_Posts extends Core_Sitemaps_Provider {
	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $object_type = 'post';
	/**
	 * Sitemap name.
	 *
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	public $name = 'posts';
	/**
	 * Sitemap route.
	 *
	 * Regex pattern used when building the route for a sitemap.
	 * Matches sitemap-posts-pages.xml, sitemap-posts-posts-20.xml.
	 *
	 * @var string
	 */
	public $route = '^sitemap-posts-([A-z]+)-?([0-9]+)?\.xml$';
	/**
	 * Sitemap slug.
	 *
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	public $slug = 'posts';

	/**
	 * Return the public post types, which excludes nav_items and similar types.
	 * Attachments are also excluded. This includes custom post types with public = true
	 */
	public function get_object_sub_types() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $post_types['attachment'] );

		/**
		 * Filter the list of post object sub types available within the sitemap.
		 *
		 * @param array $post -types List of registered object sub types.
		 *
		 * @since 0.1.0
		 */
		return apply_filters( 'core_sitemaps_post_object_sub_types', $post_types );
	}

	/**
	 * Produce XML to output.
	 */
	public function render_sitemap() {
		global $wp_query;

		$sitemap  = get_query_var( 'sitemap' );
		$sub_type = get_query_var( 'sub_type' );
		$paged    = get_query_var( 'paged' );

		$sub_types = $this->get_object_sub_types();

		if ( ! isset( $sub_types[ $sub_type ] ) ) {
			// Invalid sub type.
			$wp_query->set_404();
			status_header( 404 );

			return;
		}
		if ( $this->is_pagination_out_of_range( $paged ) ) {
			// Out of range pagination.
			$wp_query->set_404();
			status_header( 404 );

			return;
		}

		$this->sub_type = $sub_types[ $sub_type ]->name;
		if ( empty( $paged ) ) {
			$paged = 1;
		}
		if ( $this->name === $sitemap ) {
			$url_list = $this->get_url_list( $paged );
			$renderer = new Core_Sitemaps_Renderer();
			$renderer->render_sitemap( $url_list );
			exit;
		}
	}

	/**
	 * Query for the add_rewrite_rule.
	 *
	 * @return string
	 */
	public function rewrite_query() {
		return 'index.php?sitemap=' . $this->name . '&sub_type=$matches[1]&paged=$matches[2]';
	}
}
