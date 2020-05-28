<?php
/**
 * Sitemaps: WP_Sitemaps_Renderer class
 *
 * Responsible for rendering Sitemaps data to XML in accordance with sitemap protocol.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Class WP_Sitemaps_Renderer
 *
 * @since 5.5.0
 */
class WP_Sitemaps_Renderer {
	/**
	 * Gets the URL for the sitemap stylesheet.
	 *
	 * @since 5.5.0
	 *
	 * @return string The sitemap stylesheet url.
	 */
	public function get_sitemap_stylesheet_url() {
		$stylesheet_url = wp_sitemaps_get_url( 'stylesheet', get_query_var( 'sitemap' ), get_query_var( 'sitemap-sub-type' ) );

		/**
		 * Filters the URL for the sitemap stylesheet.
		 *
		 * If a falsy value is returned, no stylesheet will be used and
		 * the "raw" XML of the sitemap will be displayed.
		 *
		 * @since 5.5.0
		 *
		 * @param string $stylesheet_url Full URL for the sitemaps xsl file.
		 */
		return apply_filters( 'wp_sitemaps_stylesheet_url', $stylesheet_url );
	}

	/**
	 * Gets the URL for the sitemap index stylesheet.
	 *
	 * @since 5.5.0
	 *
	 * @return string The sitemap index stylesheet url.
	 */
	public function get_sitemap_index_stylesheet_url() {
		/* @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;

		$sitemap_url = home_url( '/wp-sitemap-index.xsl' );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$sitemap_url = add_query_arg( 'sitemap-stylesheet', 'index', home_url( '/' ) );
		}

		/**
		 * Filters the URL for the sitemap index stylesheet.
		 *
		 * If a falsy value is returned, no stylesheet will be used and
		 * the "raw" XML of the sitemap index will be displayed.
		 *
		 * @since 5.5.0
		 *
		 * @param string $sitemap_url Full URL for the sitemaps index xsl file.
		 */
		return apply_filters( 'wp_sitemaps_stylesheet_index_url', $sitemap_url );
	}

	/**
	 * Renders a sitemap index.
	 *
	 * @since 5.5.0
	 *
	 * @param array $sitemaps Array of sitemap URLs.
	 */
	public function render_index( $sitemaps ) {
		header( 'Content-type: application/xml; charset=UTF-8' );

		$this->check_for_simple_xml_availability();

		$index_xml = $this->get_sitemap_index_xml( $sitemaps );

		if ( ! empty( $index_xml ) ) {
			// All output is escaped within get_sitemap_index_xml().
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $index_xml;
		}
	}

	/**
	 * Gets XML for a sitemap index.
	 *
	 * @since 5.5.0
	 *
	 * @param array $sitemaps Array of sitemap URLs.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function get_sitemap_index_xml( $sitemaps ) {
		$stylsheet_url = $this->get_sitemap_index_stylesheet_url();
		$stylesheet_pi = '';
		if ( $stylsheet_url ) {
			$stylesheet_pi = sprintf( '<?xml-stylesheet type="text/xsl" href="%s" ?>', esc_url( $stylsheet_url ) );
		}

		$sitemap_index = new SimpleXMLElement(
			sprintf(
				'%1$s%2$s%3$s',
				'<?xml version="1.0" encoding="UTF-8" ?>',
				$stylesheet_pi,
				'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />'
			)
		);

		foreach ( $sitemaps as $entry ) {
			$sitemap = $sitemap_index->addChild( 'sitemap' );
			$sitemap->addChild( 'loc', esc_url( $entry['loc'] ) );
		}

		return $sitemap_index->asXML();
	}

	/**
	 * Renders a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param array $url_list Array of URLs for a sitemap.
	 */
	public function render_sitemap( $url_list ) {
		header( 'Content-type: application/xml; charset=UTF-8' );

		$this->check_for_simple_xml_availability();

		$sitemap_xml = $this->get_sitemap_xml( $url_list );

		if ( ! empty( $sitemap_xml ) ) {
			// All output is escaped within get_sitemap_xml().
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $sitemap_xml;
		}
	}

	/**
	 * Gets XML for a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param array $url_list Array of URLs for a sitemap.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function get_sitemap_xml( $url_list ) {
		$stylsheet_url = $this->get_sitemap_stylesheet_url();
		$stylesheet_pi = '';
		if ( $stylsheet_url ) {
			$stylesheet_pi = sprintf( '<?xml-stylesheet type="text/xsl" href="%s" ?>', $stylsheet_url );
		}

		$urlset = new SimpleXMLElement(
			sprintf(
				'%1$s%2$s%3$s',
				'<?xml version="1.0" encoding="UTF-8" ?>',
				$stylesheet_pi,
				'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />'
			)
		);

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

	/**
	 * Checks for the availability of the SimpleXML extension and errors if missing.
	 *
	 * @since 5.5.0
	 */
	private function check_for_simple_xml_availability() {
		if ( ! class_exists( 'SimpleXMLElement' ) ) {
			add_filter(
				'wp_die_handler',
				static function () {
					return '_xml_wp_die_handler';
				}
			);

			wp_die(
				sprintf(
				/* translators: %s: SimpleXML */
					__( 'Could not generate XML sitemap due to missing %s extension', 'core-sitemaps' ),
					'SimpleXML'
				),
				__( 'WordPress &rsaquo; Error', 'core-sitemaps' ),
				array(
					'response' => 501, // "Not implemented".
				)
			);
		}
	}
}
