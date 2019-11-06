<?php
/**
 * Rendering Sitemaps Data to XML.
 *
 * @package Core_Sitemap
 */

/**
 * Class Core_Sitemaps_Renderer
 */
class Core_Sitemaps_Renderer {
	public function render_index( $sitemaps_urls ) {

		header( 'Content-type: application/xml; charset=UTF-8' );

		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		foreach ( $sitemaps_urls as $link ) {
			echo $this->get_index_url_markup( $link['slug'] );
		}

		echo '</sitemapindex>';
	}

	/**
	 * Add the correct xml to any given url.
	 *
	 * @return string $markup
	 * @todo This will also need to be updated with the last modified information as well.
	 *
	 */
	public function get_index_url_markup( $url ) {
		$markup = '<sitemap>' . "\n";
		$markup .= '<loc>' . esc_url( $url ) . '</loc>' . "\n";
		$markup .= '<lastmod>2004-10-01T18:23:17+00:00</lastmod>' . "\n";
		$markup .= '</sitemap>' . "\n";

		return $markup;
	}

	/**
	 * Render a sitemap.
	 *
	 * @param WP_Post[] $content List of WP_Post objects.
	 */
	public function render_sitemap( $content ) {
		header( 'Content-type: application/xml; charset=UTF-8' );
		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		foreach ( $content as $post ) {
			$url_data = array(
				'loc'        => get_permalink( $post ),
				// DATE_W3C does not contain a timezone offset, so UTC date must be used.
				'lastmod'    => mysql2date( DATE_W3C, $post->post_modified_gmt, false ),
				'priority'   => '0.5',
				'changefreq' => 'monthly',
			);
			printf(
				'<url>
<loc>%1$s</loc>
<lastmod>%2$s</lastmod>
<changefreq>%3$s</changefreq>
<priority>%4$s</priority>
</url>',
				esc_html( $url_data['loc'] ),
				esc_html( $url_data['lastmod'] ),
				esc_html( $url_data['changefreq'] ),
				esc_html( $url_data['priority'] )
			);
		}
		echo '</urlset>';
	}
}
