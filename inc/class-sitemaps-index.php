<?php
/**
 * Class Core_Sitemaps_Index.
 * Builds the sitemap index page that lists the links to all of the sitemaps.
 *
 */
class Core_Sitemaps_Index {

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
	}

	/**
	 * Sets up rewrite rule for sitemap_index.
	 */
	public function url_rewrites() {
		add_rewrite_tag( '%sitemap%','sitemap_index' );
		add_rewrite_rule( 'sitemap\.xml$', 'index.php?sitemap=sitemap_index', 'top' );
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
	 * @param string  $sitemap_content Sitemap Links XML.
	 * @return string
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
