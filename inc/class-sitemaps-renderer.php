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
}
