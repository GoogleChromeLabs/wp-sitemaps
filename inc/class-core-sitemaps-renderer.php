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
	 * Core_Sitemaps_Renderer constructor.
	 */
	public function __construct() {
		$stylesheet_url   = $this->get_sitemap_stylesheet_url();
		$this->stylesheet = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $stylesheet_url ) . '" ?>';
	}

	/**
	 * Get the URL for a specific sitemap.
	 *
	 * @param string $name The name of the sitemap to get a URL for.
	 * @return string the sitemap index url.
	 */
	public function get_sitemap_url( $name ) {
		global $wp_rewrite;

		$home_url_append = '';
		if ( 'index' !== $name ) {
			$home_url_append = '-' . $name;
		}
		$url = home_url( sprintf( '/sitemap%1$s.xml', $home_url_append ) );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$url = add_query_arg( 'sitemap', $name, home_url( '/' ) );
		}

		return $url;
	}

	/**
	 * Get the URL for the sitemap stylesheet.
	 *
	 * @return string the sitemap stylesheet url.
	 */
	public function get_sitemap_stylesheet_url() {
		return plugin_dir_url( __FILE__ ) . 'sitemap.xsl';
	}

	/**
	 * Render a sitemap index.
	 *
	 * @param array $sitemaps List of sitemaps, see \Core_Sitemaps_Registry::$sitemaps.
	 */
	public function render_index( $sitemaps ) {
		header( 'Content-type: application/xml; charset=UTF-8' );
		$sitemap_index = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?>' . $this->stylesheet . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>' );

		foreach ( $sitemaps as $slug ) {
			$sitemap = $sitemap_index->addChild( 'sitemap' );
			$sitemap->addChild( 'loc', esc_url( $this->get_sitemap_url( $slug ) ) );
			$sitemap->addChild( 'lastmod', '2004-10-01T18:23:17+00:00' );
		}
		// All output is escaped within the addChild method calls.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $sitemap_index->asXML();

		/**
		 * Filter the URL for the sitemap stylesheet'.
		 *
		 * @param string $stylesheet Full XML-Stylesheet declaration with URL.
		 */
		return apply_filters( 'core_sitemaps_stylesheet', $this->stylesheet );
	}

	/**
	 * Render a sitemap.
	 *
	 * @param array $url_list A list of URLs for a sitemap.
	 */
	public function render_sitemap( $url_list ) {
		global $wp_query;

		header( 'Content-type: application/xml; charset=UTF-8' );
		$urlset = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?>' . $this->stylesheet . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>' );

		if ( empty( $url_list ) ) {
			$wp_query->set_404();
			status_header( 404 );
		}

		foreach ( $url_list as $url_item ) {
			$url = $urlset->addChild( 'url' );
			$url->addChild( 'loc', esc_url( $url_item['loc'] ) );
			$url->addChild( 'lastmod', esc_attr( $url_item['lastmod'] ) );
		}

		// All output is escaped within the addChild method calls.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $urlset->asXML();

		/**
		 * Filter the URL for the sitemap stylesheet'.
		 *
		 * @param string $stylesheet Full XML-Stylesheet declaration with URL.
		 */
		return apply_filters( 'core_sitemaps_stylesheet', $this->stylesheet );
	}
}
