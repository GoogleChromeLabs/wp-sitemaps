<?php
/**
 * Rendering Sitemaps Data to XML in accordance with sitemap protocol.
 *
 * @package Core_Sitemap
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
		$sitemap_url = home_url( 'sitemap.xsl' );

		/**
		 * Filter the URL for the sitemap stylesheet'.
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
		$sitemap_url = home_url( 'sitemap-index.xsl' );

		/**
		 * Filter the URL for the sitemap index stylesheet'.
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
		$sitemap_index = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?>' . $this->stylesheet_index . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>' );

		foreach ( $sitemaps as $entry ) {
			$sitemap = $sitemap_index->addChild( 'sitemap' );
			$sitemap->addChild( 'loc', esc_url( $entry['loc'] ) );
			$sitemap->addChild( 'lastmod', esc_html( $entry['lastmod'] ) );
		}

		// All output is escaped within the addChild method calls.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $sitemap_index->asXML();
	}

	/**
	 * Render a sitemap.
	 *
	 * @param array $url_list A list of URLs for a sitemap.
	 */
	public function render_sitemap( $url_list ) {
		header( 'Content-type: application/xml; charset=UTF-8' );
		$urlset = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?>' . $this->stylesheet . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>' );

		foreach ( $url_list as $url_item ) {
			$url = $urlset->addChild( 'url' );
			$url->addChild( 'loc', esc_url( $url_item['loc'] ) );
			$url->addChild( 'lastmod', esc_attr( $url_item['lastmod'] ) );
		}

		// All output is escaped within the addChild method calls.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $urlset->asXML();
	}
}
