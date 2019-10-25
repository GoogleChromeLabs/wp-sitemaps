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
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'url_rewrites' ), 99 );
		add_filter( 'redirect_canonical', array( $this, 'redirect_canonical' ) );
		add_action( 'template_include', array( $this, 'output_sitemap' ) );
	}

	/**
	 * Sets up rewrite rule for sitemap_index.
	 * @todo Additional rewrites will probably need adding to this.
	 */
	public function url_rewrites() {
		global $wp;
		$wp->add_query_var( 'sitemap' );

		add_rewrite_rule( '^sitemap\.xml$', 'index.php?sitemap=sitemap', 'top' );
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
	 * @todo Split this into seperate functions to apply headers, <xml> tag and <sitemapindex> tag if this is an index?
	 */
	public function output_sitemap( $sitemap_content ) {
		$sitemap_index = get_query_var( 'sitemap' );

		if ( ! empty( $sitemap_index ) ) {
			header( 'Content-type: application/xml; charset=UTF-8' );

			$output = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

			$output .= $sitemap_content;
			$output .= '</sitemapindex>';

			return $output;
		}
	}
}
