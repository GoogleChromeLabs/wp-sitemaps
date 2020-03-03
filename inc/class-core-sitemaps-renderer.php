<?php
/**
 * Sitemaps: Core_Sitemaps_Renderer class
 *
 * Responsible for rendering Sitemaps data to XML in accordance with sitemap protocol.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since x.x.x
 */

/**
 * Class Core_Sitemaps_Renderer
 */
class Core_Sitemaps_Renderer {
	/**
	 * XSL stylesheet for styling a sitemap for web browsers.
	 *
	 * @var string
	 */
	protected $stylesheet = '';

	/**
	 * XSL stylesheet for styling a sitemap for web browsers.
	 *
	 * @var string
	 */
	protected $stylesheet_index = '';

	/**
	 * Core_Sitemaps_Renderer constructor.
	 */
	public function __construct() {
		$stylesheet_url         = $this->get_sitemap_stylesheet_url();
		$stylesheet_index_url   = $this->get_sitemap_index_stylesheet_url();
		$this->stylesheet       = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $stylesheet_url ) . '" ?>';
		$this->stylesheet_index = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $stylesheet_index_url ) . '" ?>';
	}

	/**
	 * Get the URL for the sitemap stylesheet.
	 *
	 * @return string the sitemap stylesheet url.
	 */
	public function get_sitemap_stylesheet_url() {
		$sitemap_url = home_url( '/wp-sitemap.xsl' );

		/**
		 * Filter the URL for the sitemap stylesheet.
		 *
		 * @param string $sitemap_url Full URL for the sitemaps xsl file.
		 */
		return apply_filters( 'core_sitemaps_stylesheet_url', $sitemap_url );
	}

	/**
	 * Get the URL for the sitemap index stylesheet.
	 *
	 * @return string the sitemap index stylesheet url.
	 */
	public function get_sitemap_index_stylesheet_url() {
		$sitemap_url = home_url( '/wp-sitemap-index.xsl' );

		/**
		 * Filter the URL for the sitemap index stylesheet.
		 *
		 * @param string $sitemap_url Full URL for the sitemaps index xsl file.
		 */
		return apply_filters( 'core_sitemaps_stylesheet_index_url', $sitemap_url );
	}

	/**
	 * Render a sitemap index.
	 *
	 * @param array $sitemaps List of sitemap entries including loc and lastmod data.
	 */
	public function render_index( $sitemaps ) {
		header( 'Content-type: application/xml; charset=UTF-8' );

		$index_xml = $this->get_sitemap_index_xml( $sitemaps );

		if ( ! empty( $index_xml ) ) {
			// All output is escaped within get_sitemap_index_xml().
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $index_xml;
		}
	}

	/**
	 * Get XML for a sitemap index.
	 *
	 * @param array $sitemaps List of sitemap entries including loc and lastmod data.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function get_sitemap_index_xml( $sitemaps ) {
		$sitemap_index = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?>' . $this->stylesheet_index . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>' );

		foreach ( $sitemaps as $entry ) {
			$sitemap = $sitemap_index->addChild( 'sitemap' );
			$sitemap->addChild( 'loc', esc_url( $entry['loc'] ) );
			$sitemap->addChild( 'lastmod', esc_html( $entry['lastmod'] ) );
		}

		return $sitemap_index->asXML();
	}

	/**
	 * Render a sitemap.
	 *
	 * @param array $url_list A list of URLs for a sitemap.
	 */
	public function render_sitemap( $url_list ) {
		header( 'Content-type: application/xml; charset=UTF-8' );

		$sitemap_xml = $this->get_sitemap_xml( $url_list );

		if ( ! empty( $sitemap_xml ) ) {
			// All output is escaped within get_sitemap_xml().
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $sitemap_xml;
		}
	}

	/**
	 * Get XML for a sitemap.
	 *
	 * @param array $url_list A list of URLs for a sitemap.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function get_sitemap_xml( $url_list ) {
		$urlset = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?>' . $this->stylesheet . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>' );

		foreach ( $url_list as $url_item ) {
			$url = $urlset->addChild( 'url' );

			// Add each attribute as a child node to the URL entry.
			foreach ( $url_item as $attr => $value ) {
				if ( 'url' === $attr ) {
					$url->addChild( $attr, esc_url( $value ) );
				} else {
					$url->addChild( $attr, esc_attr( $value ) );
				}
			}
		}

		return $urlset->asXML();
	}
}
