<?php
/**
 * Sitemaps: Core_Sitemaps_Index class
 *
 * This class generates the sitemap index.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since x.x.x
 */

/**
 * Class Core_Sitemaps_Index.
 * Builds the sitemap index page that lists the links to all of the sitemaps.
 */
class Core_Sitemaps_Index {
	/**
	 * Sitemap name.
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	protected $name = 'index';

	/**
	 * Builds the URL for the sitemap index.
	 *
	 * @return string the sitemap index url.
	 */
	public function get_index_url() {
		/* @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;

		$url = home_url( '/wp-sitemap.xml' );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$url = add_query_arg( 'sitemap', 'index', home_url( '/' ) );
		}

		return $url;
	}
}
