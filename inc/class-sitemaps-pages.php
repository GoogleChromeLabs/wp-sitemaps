<?php

/**
 * Class Core_Sitemaps_Posts.
 * Builds the sitemap pages for Posts.
 */
class Core_Sitemaps_Pages extends Core_Sitemaps_Provider {
	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $object_type = 'page';
	/**
	 * Sitemap name
	 *
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	public $name = 'pages';
	/**
	 * Sitemap route.
	 *
	 * Regex pattern used when building the route for a sitemap.
	 *
	 * @var string
	 */
	public $route = '^sitemap-pages\.xml$';
	/**
	 * Sitemap slug.
	 *
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	public $slug = 'pages';

	/**
	 * Produce XML to output.
	 */
	public function render_sitemap() {
		$sitemap = get_query_var( 'sitemap' );
		$paged   = get_query_var( 'paged' );

		if ( empty( $paged ) ) {
			$paged = 1;
		}

		if ( 'pages' === $sitemap ) {
			$url_list = $this->get_url_list( $paged );
			$renderer = new Core_Sitemaps_Renderer();
			$renderer->render_sitemap( $url_list );
			exit;
		}
	}
}
