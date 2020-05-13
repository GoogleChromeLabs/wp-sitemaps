<?php
/**
 * Sitemaps: Core_Sitemaps_Index class.
 *
 * Generates the sitemap index.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Class Core_Sitemaps_Index.
 * Builds the sitemap index page that lists the links to all of the sitemaps.
 *
 * @since 5.5.0
 */
class Core_Sitemaps_Index {

	/**
	 * The main registry of supported sitemaps.
	 *
	 * @since 5.5.0
	 * @var Core_Sitemaps_Registry
	 */
	protected $registry;

	/**
	 * Core_Sitemaps_Index constructor.
	 *
	 * @since 5.5.0
	 *
	 * @param Core_Sitemaps_Registry $registry Sitemap provider registry.
	 */
	public function __construct( $registry ) {
		$this->registry = $registry;
	}

	/**
	 * Gets a sitemap list for the index.
	 *
	 * @since 5.5.0
	 *
	 * @return array List of all sitemaps.
	 */
	public function get_sitemap_list() {
		$sitemaps = array();

		$providers = $this->registry->get_sitemaps();
		/* @var Core_Sitemaps_Provider $provider */
		foreach ( $providers as $provider ) {
			// Using array_push is more efficient than array_merge in a loop.
			array_push( $sitemaps, ...$provider->get_sitemap_entries() );
		}

		return $sitemaps;
	}

	/**
	 * Builds the URL for the sitemap index.
	 *
	 * @since 5.5.0
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
