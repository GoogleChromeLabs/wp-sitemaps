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
	 * Renderer class.
	 *
	 * @var Core_Sitemaps_Renderer
	 */
	protected $renderer;

	/**
	 * Core_Sitemaps_Index constructor.
	 */
	public function __construct() {
		$this->renderer = new Core_Sitemaps_Renderer();
	}

	/**
	 * A helper function to initiate actions, hooks and other features needed.
	 */
	public function setup_sitemap() {
		// Set up rewrites.
		add_rewrite_tag( '%sitemap%', '([^?]+)' );
		add_rewrite_rule( '^sitemap\.xml$', 'index.php?sitemap=index', 'top' );

		// Add filters.
		add_filter( 'robots_txt', array( $this, 'add_robots' ), 0, 2 );
		add_filter( 'redirect_canonical', array( $this, 'redirect_canonical' ) );

		// Add actions.
		add_action( 'template_redirect', array( $this, 'render_sitemap' ) );
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
	 * Produce XML to output.
	 */
	public function render_sitemap() {
		$sitemap_index = get_query_var( 'sitemap' );

		if ( 'index' === $sitemap_index ) {
			$providers = core_sitemaps_get_sitemaps();

			$sitemaps = array();

			foreach ( $providers as $provider ) {
				// Using array_push is more efficient than array_merge in a loop.
				array_push( $sitemaps, ...$provider->get_sitemap_entries() );
			}

			$this->renderer->render_index( $sitemaps );
			exit;
		}
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
