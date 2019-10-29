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
	 * Sets up rewrite rule for sitemap_index.
	 */
	public function url_rewrites() {
		add_rewrite_tag( '%sitemap%', 'sitemap' );
		$registry = Core_Sitemaps_Registry::instance();
		$registry->add_sitemap( 'sitemap', '^sitemap\.xml$' );
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
	 * @todo Review later how $sitemap_content gets pulled in here to display the list of links.
	 * @todo Split this into seperate functions to apply headers, <xml> tag and <sitemapindex> tag if this is an index?
	 */
	public function output_sitemap( $template ) {
		$sitemap_index = get_query_var( 'sitemap' );

		if ( ! empty( $sitemap_index ) ) {
			header( 'Content-type: application/xml; charset=UTF-8' );

			$output = '<?xml version="1.0" encoding="UTF-8" ?>';
			$output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

			$output .= '</sitemapindex>';

			return $output;
		}
		return $template;
	}
}
