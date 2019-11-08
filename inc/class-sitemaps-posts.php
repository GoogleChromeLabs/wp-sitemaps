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
	 *
	 * @var string
	 */
	public $route = '^sitemap-posts\.xml$';

	/**
	 * Sitemap slug.
	 *
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	public $slug = 'posts';

	/**
	 * Produce XML to output.
	 */
	public function render_sitemap() {
		$sitemap = get_query_var( 'sitemap' );
		$paged   = get_query_var( 'paged' );

		if ( 'posts' === $sitemap ) {
			$content  = $this->get_content_per_page( $this->object_type, $paged );
			$renderer = new Core_Sitemaps_Renderer();
			$renderer->render_urlset( $content );
			exit;
		}
	}
}
