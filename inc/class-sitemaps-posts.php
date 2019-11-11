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
	 * Attachments are also excluded.
	 */
	public function get_sitemap_sub_types() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		if ( isset( $post_types['attachment'] ) ) {
			unset( $post_types['attachment'] );
		}

		return $post_types;
	}

	/**
	 * Produce XML to output.
	 */
	public function render_sitemap() {
		$sitemap  = get_query_var( 'sitemap' );
		$sub_type = get_query_var( 'sub_type' );
		$paged    = get_query_var( 'paged' );

		$sub_types = $this->get_sitemap_sub_types();

		if ( ! isset( $sub_types[ $sub_type ] ) ) {
			// FIXME: issue 404 when the object subtype isn't valid.
			return;
		}

		// FIXME: issue 404 when the paged value is out of range.

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
