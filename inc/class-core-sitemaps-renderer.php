<?php
/**
 * Sitemaps: Core_Sitemaps_Renderer class
 *
 * Responsible for rendering Sitemaps data to XML in accordance with sitemap protocol.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Class Core_Sitemaps_Renderer
 *
 * @since 5.5.0
 */
class Core_Sitemaps_Renderer {
	/**
	 * XSL stylesheet for styling a sitemap for web browsers.
	 *
	 * @since 5.5.0
	 *
	 * @var string
	 */
	protected $stylesheet = '';

	/**
	 * XSL stylesheet for styling a sitemap for web browsers.
	 *
	 * @since 5.5.0
	 *
	 * @var string
	 */
	protected $stylesheet_index = '';

	/**
	 * Core_Sitemaps_Renderer constructor.
	 *
	 * @since 5.5.0
	 */
	public function __construct() {
		$stylesheet_url = $this->get_sitemap_stylesheet_url();
		if ( $stylesheet_url ) {
			$this->stylesheet = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $stylesheet_url ) . '" ?>';
		}
		$stylesheet_index_url   = $this->get_sitemap_index_stylesheet_url();
		if ( $stylesheet_index_url ) {
			$this->stylesheet_index = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $stylesheet_index_url ) . '" ?>';
		}
	}

	/**
	 * Gets the URL for the sitemap stylesheet.
	 *
	 * @since 5.5.0
	 *
	 * @return string the sitemap stylesheet url.
	 */
	public function get_sitemap_stylesheet_url() {
		/* @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;

		$sitemap_url = home_url( '/wp-sitemap.xsl' );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$sitemap_url = add_query_arg( 'sitemap-stylesheet', 'sitemap', home_url( '/' ) );
		}

		/**
		 * Filters the URL for the sitemap stylesheet.
		 *
		 * If a falsy value is returned, no stylesheet will be used and
		 * the "raw" XML of the sitemap will be displayed.
		 *
		 * @since 5.5.0
		 *
		 * @param string $sitemap_url Full URL for the sitemaps xsl file.
		 */
		return apply_filters( 'core_sitemaps_stylesheet_url', $sitemap_url );
	}

	/**
	 * Gets the URL for the sitemap index stylesheet.
	 *
	 * @since 5.5.0
	 *
	 * @return string the sitemap index stylesheet url.
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
		return apply_filters( 'core_sitemaps_stylesheet_index_url', $sitemap_url );
	}

	/**
	 * Renders a sitemap index.
	 *
	 * @since 5.5.0
	 *
	 * @param array $sitemaps List of sitemap entries.
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
	 * @param array $sitemaps List of sitemap entries.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function get_sitemap_index_xml( $sitemaps ) {
		$sitemap_index = new SimpleXMLElement(
			sprintf(
				'%1$s%2$s%3$s',
				'<?xml version="1.0" encoding="UTF-8" ?>',
				$this->stylesheet_index,
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
	 * @param array $url_list A list of URLs for a sitemap.
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
	 * @param array $url_list A list of URLs for a sitemap.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function get_sitemap_xml( $url_list ) {
		$urlset = new SimpleXMLElement(
			sprintf(
				'%1$s%2$s%3$s',
				'<?xml version="1.0" encoding="UTF-8" ?>',
				$this->stylesheet,
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
