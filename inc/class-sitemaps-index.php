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
class Core_Sitemaps_Index extends Core_Sitemaps_Provider {
	/**
	 * Sitemap name
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	protected $name = 'index';

	/**
	 *
	 * A helper function to initiate actions, hooks and other features needed.
	 *
	 * @uses add_action()
	 * @uses add_filter()
	 */
	public function bootstrap() {
		add_action( 'core_sitemaps_setup_sitemaps', array( $this, 'register_sitemap' ), 99 );
		add_filter( 'robots_txt', array( $this, 'add_robots' ), 0, 2 );
		add_filter( 'redirect_canonical', array( $this, 'redirect_canonical' ) );
		add_action( 'template_redirect', array( $this, 'render_sitemap' ) );
	}

	/**
	 * Sets up rewrite rule for sitemap_index.
	 */
	public function register_sitemap() {
		$this->registry->add_sitemap( $this->name, 'sitemap\.xml$', esc_url( $this->get_sitemap_url( $this->name ) ) );
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
	 *
	 * @todo At the moment this outputs the rewrite rule for each sitemap rather than the URL.
	 * This will need changing.
	 *
	 */
	public function render_sitemap() {
		$sitemap_index = get_query_var( 'sitemap' );

		if ( 'index' === $sitemap_index ) {
			$sitemaps_urls = $this->registry->get_sitemaps();
			$renderer      = new Core_Sitemaps_Renderer();
			$renderer->render_sitemapindex( $sitemaps_urls );
			exit;
		}
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
			$output .= 'Sitemap: ' . esc_url( $this->get_sitemap_url( $this->name ) ) . "\n";
		}
		return $output;
	}
}
