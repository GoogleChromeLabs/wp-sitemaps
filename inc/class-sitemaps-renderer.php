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
	 * Render a sitemap index.
	 *
	 * @param array $sitemaps List of sitemaps, see \Core_Sitemaps_Registry::$sitemaps.
	 */
	public function render_index( $sitemaps ) {
		header( 'Content-type: application/xml; charset=UTF-8' );
		$sitemap_index = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>' );

		foreach ( $sitemaps as $link ) {
			$sitemap = $sitemap_index->addChild( 'sitemap' );
			$sitemap->addChild( 'loc', esc_url( $this->get_sitemap_url( $link->slug ) ) );
			$sitemap->addChild( 'lastmod', '2004-10-01T18:23:17+00:00' );
		}
		echo $sitemap_index->asXML();
	}

	/**
	 * Render a sitemap urlset.
	 *
	 * @param WP_Post[] $content List of WP_Post objects.
	 */
	public function render_urlset( $content ) {
		header( 'Content-type: application/xml; charset=UTF-8' );
		$urlset = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>' );

		foreach ( $content as $post ) {
			$url = $urlset->addChild( 'url' );
			$url->addChild( 'loc', esc_url( get_permalink( $post ) ) );
			$url->addChild( 'lastmod', mysql2date( DATE_W3C, $post->post_modified_gmt, false ) );
			$url->addChild( 'priority', '0.5' );
			$url->addChild( 'changefreq', 'monthly' );
		}
		echo $urlset->asXML();
	}
}
