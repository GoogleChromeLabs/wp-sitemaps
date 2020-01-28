<?php
/**
 * Class file for the Core_Sitemaps_Index class.
 * This class generates the sitemap index.
 *
 * @package Core_Sitemaps
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
	 * A helper function to initiate actions, hooks and other features needed.
	 */
	public function setup_sitemap() {
		// Add filters.
		add_filter( 'robots_txt', array( $this, 'add_robots' ), 0, 2 );
		add_filter( 'redirect_canonical', array( $this, 'redirect_canonical' ) );
	}

	/**
	 * Prevent trailing slashes.
	 *
	 * @param string $redirect The redirect URL currently determined.
	 * @return bool|string $redirect
	 */
	public function redirect_canonical( $redirect ) {
		if ( get_query_var( 'sitemap' ) ) {
			return false;
		}

		return $redirect;
	}

	/**
	 * Builds the URL for the sitemap index.
	 *
	 * @return string the sitemap index url.
	 */
	public function get_index_url() {
		global $wp_rewrite;

		$url = home_url( '/sitemap.xml' );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$url = add_query_arg( 'sitemap', 'index', home_url( '/' ) );
		}

		return $url;
	}

	/**
	 * Adds the sitemap index to robots.txt.
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
