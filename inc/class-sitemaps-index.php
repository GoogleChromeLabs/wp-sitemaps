<?php
/**
 * Class Core_Sitemaps_Index.
 * Builds the sitemap index page that lists the links to all of the sitemaps.
 *
 * @todo This will probably be split out so that rewrites are in a class, building the xml output is a class,
 * rendering sitemaps content is a class etc.
 */
class Core_Sitemaps_Index {

	/**
	 * Content of the sitemap to output.
	 *
	 * @var string
	 */
	protected $sitemap_content = '';

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
		add_filter( 'template_include', array( $this, 'output_sitemap' ) );
	}

	/**
	 * Sets up rewrite rule for sitemap_index.
	 * @todo Additional rewrites will probably need adding to this.
	 */
	public function url_rewrites() {
		add_rewrite_tag( '%sitemap%','sitemap' );
		add_rewrite_rule( 'sitemap_index\.xml$', 'index.php?sitemap=sitemap', 'top' );
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
	 * @param string  $template The template to return. Either custom XML or default.
	 * @return string
	 *
	 * @todo Review how the sitemap files are built and placed in the root of the site.
	 * @todo Split this into seperate functions to apply headers, <xml> tag and <sitemapindex> tag if this is an index?
	 */
	public function output_sitemap( $template ) {
		$sitemap_index = get_query_var( 'sitemap' );

		if ( ! empty( $sitemap_index ) ) {
			wp_redirect( home_url( 'wp-content/plugins/core-sitemaps/inc/sitemap_index.xml' ), 301, 'Yoast SEO' );
			exit;
		}

		return $template;
	}
}
