<?php
/**
 * Class Core_Sitemaps_Index.
 * Builds the sitemap index page that lists the links to all of the sitemaps.
 *
 */
class Core_Sitemaps_Index {
	/**
	 * @var Core_Sitemaps_Registry object
	 */
	public $registry;

	/**
	 * Core_Sitemaps_Index constructor.
	 */
	public function __construct() {
		$this->registry = Core_Sitemaps_Registry::instance();
	}

	/**
	 *
	 * A helper function to initiate actions, hooks and other features needed.
	 *
	 * @uses add_action()
	 * @uses add_filter()
	 */
	public function bootstrap() {
		add_action( 'init', array( $this, 'url_rewrites' ), 99 );
		add_filter( 'redirect_canonical', array( $this, 'redirect_canonical' ) );
		add_action( 'template_redirect', array( $this, 'output_sitemap' ) );

		// FIXME: Move this into a Core_Sitemaps class registration system.
		$core_sitemaps_posts = new Core_Sitemaps_Posts();
		$core_sitemaps_posts->bootstrap();
	}

	/**
	 * Sets up rewrite rule for sitemap_index.
	 */
	public function url_rewrites() {
		$this->registry->add_sitemap( 'sitemap_index', 'sitemap\.xml$' );
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
	 * @return void
	 *
	 */
	public function output_sitemap() {
		$sitemap_index = get_query_var( 'sitemap' );

		if ( 'sitemap_index' === $sitemap_index ) {
			header( 'Content-type: application/xml; charset=UTF-8' );

			echo '<?xml version="1.0" encoding="UTF-8" ?>';
			echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

			echo '</sitemapindex>';
			exit;
		}
	}
}
