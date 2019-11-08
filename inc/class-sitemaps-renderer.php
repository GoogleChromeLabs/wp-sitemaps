<?php
/**
 * Rendering Sitemaps Data to XML in accorance with sitemap protocol.
 *
 * @package Core_Sitemap
 */

/**
 * Class Core_Sitemaps_Renderer
 */
class Core_Sitemaps_Renderer {
	/**
	 * Render a sitemap index.
	 *
	 * @param array $sitemaps List of sitemaps, see \Core_Sitemaps_Registry::$sitemaps.
	 */
	public function render_index( $sitemaps ) {
		header( 'Content-type: application/xml; charset=UTF-8' );
		$sitemap_index = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>' );

		foreach ( $sitemaps as $link ) {
			$sitemap = $sitemap_index->addChild( 'sitemap' );
			$sitemap->addChild( 'loc', esc_url( $link->slug ) );
			$sitemap->addChild( 'lastmod', '2004-10-01T18:23:17+00:00' );
		}
		echo $sitemap_index->asXML();
	}

	/**
	 * Render a sitemap.
	 *
	 * @param array $url_list A list of URLs for a sitemap.
	 */
	public function render_sitemap( $url_list ) {
		header( 'Content-type: application/xml; charset=UTF-8' );
		$urlset = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>' );
		foreach ( $url_list as $url_item ) {
			$url = $urlset->addChild( 'url' );
			$url->addChild( 'loc', esc_url( $url_item['loc'] ) );
			$url->addChild( 'lastmod', esc_attr( $url_item['lastmod'] ) );
			$url->addChild( 'priority', esc_attr( $url_item['priority'] ) );
			$url->addChild( 'changefreq', esc_attr( $url_item['changefreq' ] ) );
		}
		echo $urlset->asXML();
	}
}
