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
	 * Sitemap name.
	 * Used for building sitemap URLs.
	 *
	 * @since 5.5.0
	 *
	 * @var string
	 */
	protected $name = 'index';

	/**
	 * Initiates actions, hooks and other features needed.
	 *
	 * @since 5.5.0
	 */
	public function setup_sitemap() {
		// Add filters.
		add_filter( 'robots_txt', array( $this, 'add_robots' ), 0, 2 );
		add_filter( 'redirect_canonical', array( $this, 'redirect_canonical' ) );
	}

	/**
	 * Prevents trailing slashes.
	 *
	 * @since 5.5.0
	 *
	 * @param string $redirect The redirect URL currently determined.
	 * @return bool|string $redirect
	 */
	public function redirect_canonical( $redirect ) {
		if ( get_query_var( 'sitemap' ) || get_query_var( 'sitemap-stylesheet' ) ) {
			return false;
		}

		return $redirect;
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

	/**
	 * Adds the sitemap index to robots.txt.
	 *
	 * @since 5.5.0
	 *
	 * @param string $output robots.txt output.
	 * @param bool   $public Whether the site is public or not.
	 * @return string robots.txt output.
	 */
	public function add_robots( $output, $public ) {
		if ( $public ) {
			$output .= "\nSitemap: " . esc_url( $this->get_index_url() ) . "\n";
		}

		return $output;
	}
}
