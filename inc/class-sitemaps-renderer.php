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
	public function render_sitemapindex( $sitemaps ) {
		header( 'Content-type: application/xml; charset=UTF-8' );
		$sitemap_index = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>' );

		foreach ( $sitemaps as $link ) {
			$sitemap = $sitemap_index->addChild( 'sitemap' );
			$sitemap->addChild( 'loc', esc_url( $link['slug'] ) );
			$sitemap->addChild( 'lastmod', '2004-10-01T18:23:17+00:00' );
		}
		echo $sitemap_index->asXML();
	}

	/**
	 * Render a sitemap urlset.
	 *
	 * @param WP_Post[] $content List of WP_Post objects.
	 */
	public function render_urlset( $content, $object_type ) {
		header( 'Content-type: application/xml; charset=UTF-8' );
		$urlset = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>' );

		foreach ( $content as $post ) {
			$url = $urlset->addChild( 'url' );
			if ( 'category' === $object_type ) {
				$url->addChild( 'loc', esc_url( get_category_link( $post->term_id ) ) );
			} else {
				$url->addChild( 'loc', esc_url( get_permalink( $post ) ) );
			}
			$url->addChild( 'lastmod', mysql2date( DATE_W3C, $post->post_modified_gmt, false ) );
			$url->addChild( 'priority', '0.5' );
			$url->addChild( 'changefreq', 'monthly' );
		}
		echo $urlset->asXML();
	}
}
