<?php
/**
 * Pages sitemap.
 *
 * @package Core_Sitemaps
 */

/**
 * Class Core_Sitemaps_Pages.
 * Builds the sitemap pages for Pages.
 */
class Core_Sitemaps_Pages extends Core_Sitemaps_Provider {
	/**
	 * Core_Sitemaps_Pages constructor.
	 */
	public function __construct() {
		$this->object_type = 'page';
		$this->name        = 'pages';
		$this->route       = '^sitemap-pages\.xml$';
		$this->slug        = 'pages';
	}

	/**
	 * Produce XML to output.
	 *
	 * @noinspection PhpUnused
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
