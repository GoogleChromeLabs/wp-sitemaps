<?php
/**
 * Sitemaps: Public functions
 *
 * This file contains a variety of public functions developers can use to interact with
 * the XML Sitemaps API.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Retrieves the current Sitemaps server instance.
 *
 * @since 5.5.0
 *
 * @return WP_Sitemaps|null Sitemaps instance, or null if sitemaps are disabled.
 */
function wp_sitemaps_get_server() {
	/**
	 * Global Core Sitemaps instance.
	 *
	 * @since 5.5.0
	 *
	 * @var WP_Sitemaps $wp_sitemaps
	 */
	global $wp_sitemaps;

	$is_enabled = (bool) get_option( 'blog_public' );

	/**
	 * Filters whether XML Sitemaps are enabled or not.
	 *
	 * @since 5.5.0
	 *
	 * @param bool $is_enabled Whether XML Sitemaps are enabled or not. Defaults to true for public sites.
	 */
	$is_enabled = (bool) apply_filters( 'wp_sitemaps_enabled', $is_enabled );

	if ( ! $is_enabled ) {
		return null;
	}

	// If there isn't a global instance, set and bootstrap the sitemaps system.
	if ( empty( $wp_sitemaps ) ) {
		$wp_sitemaps = new WP_Sitemaps();
		$wp_sitemaps->init();

		/**
		 * Fires when initializing the Sitemaps object.
		 *
		 * Additional sitemaps should be registered on this hook.
		 *
		 * @since 5.5.0
		 *
		 * @param WP_Sitemaps $sitemaps Server object.
		 */
		do_action( 'wp_sitemaps_init', $wp_sitemaps );
	}

	return $wp_sitemaps;
}

/**
 * Gets a list of sitemap providers.
 *
 * @since 5.5.0
 *
 * @return array $sitemaps A list of registered sitemap providers.
 */
function wp_get_sitemaps() {
	$sitemaps = wp_sitemaps_get_server();

	if ( ! $sitemaps ) {
		return array();
	}

	return $sitemaps->registry->get_sitemaps();
}

/**
 * Registers a new sitemap provider.
 *
 * @since 5.5.0
 *
 * @param string               $name     Unique name for the sitemap provider.
 * @param WP_Sitemaps_Provider $provider The `Sitemaps_Provider` instance implementing the sitemap.
 * @return bool Returns true if the sitemap was added. False on failure.
 */
function wp_register_sitemap( $name, WP_Sitemaps_Provider $provider ) {
	$sitemaps = wp_sitemaps_get_server();

	if ( ! $sitemaps ) {
		return false;
	}

	return $sitemaps->registry->add_sitemap( $name, $provider );
}

/**
 * Gets the maximum number of URLs for a sitemap.
 *
 * @since 5.5.0
 *
 * @param string $object_type Object type for sitemap to be filtered (e.g. 'post', 'term', 'user').
 * @return int The maximum number of URLs.
 */
function wp_sitemaps_get_max_urls( $object_type ) {
	/**
	 * Filters the maximum number of URLs displayed on a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param int    $max_urls    The maximum number of URLs included in a sitemap. Default 2000.
	 * @param string $object_type Object type for sitemap to be filtered (e.g. 'post', 'term', 'user').
	 */
	return apply_filters( 'wp_sitemaps_max_urls', WP_SITEMAPS_MAX_URLS, $object_type );
}

if ( ! function_exists( 'esc_xml' ) ) :
	/**
	 * Escaping for XML blocks.
	 *
	 * @since 5.5.0
	 *
	 * @param string $text Text to escape.
	 * @return string
	 */
	function esc_xml( $text ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
		$safe_text = wp_check_invalid_utf8( $text );

		$cdata_regex = '\<\!\[CDATA\[.*?\]\]\>';
		$regex       = <<<EOF
/
	(?=.*?{$cdata_regex})                 # lookahead that will match anything followed by a CDATA Section
	(?<non_cdata_followed_by_cdata>(.*?)) # the "anything" matched by the lookahead
	(?<cdata>({$cdata_regex}))            # the CDATA Section matched by the lookahead

|	                                      # alternative

	(?<non_cdata>(.*))                    # non-CDATA Section
/sx 
EOF;
		$safe_text = (string) preg_replace_callback(
			$regex,
			function( $matches ) {
				if ( ! $matches[0] ) {
					return '';
				} elseif ( ! empty( $matches['non_cdata'] ) ) {
					// escape HTML entities in the non-CDATA Section.
					return _esc_xml_non_cdata_section( $matches['non_cdata'] );
				}

				// Return the CDATA Section unchanged, escape HTML entities in the rest.
				return _esc_xml_non_cdata_section( $matches['non_cdata_followed_by_cdata'] ) . $matches['cdata'];
			},
			$safe_text
		);

		/**
		 * Filters a string cleaned and escaped for output in XML.
		 *
		 * Text passed to esc_xml() is stripped of invalid or special characters
		 * before output. HTML named character references are converted to their
		 * equivalent code points.
		 *
		 * @since 5.5.0
		 *
		 * @param string $safe_text The text after it has been escaped.
		 * @param string $text      The text prior to being escaped.
		 */
		return apply_filters( 'esc_xml', $safe_text, $text ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
	}
endif;

if ( ! function_exists( '_esc_xml_non_cdata_section' ) ) :
	/**
	 * Escaping for non-CDATA Section XML blocks.
	 *
	 * @access private
	 * @since 5.5.0
	 *
	 * @param string $text Text to escape.
	 * @return string
	 */
	function _esc_xml_non_cdata_section( $text ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
		global $allowedentitynames;

		$safe_text = _wp_specialchars( $text, ENT_QUOTES );
		// Replace HTML entities with their Unicode codepoints,
		// without doing the same for the 5 XML entities.
		$html_only_entities = array_diff( $allowedentitynames, array( 'amp', 'lt', 'gt', 'apos', 'quot' ) );
		$safe_text          = (string) preg_replace_callback(
			'/&(' . implode( '|', $html_only_entities ) . ');/',
			function( $matches ) {
				return html_entity_decode( $matches[0], ENT_HTML5 );
			},
			$safe_text
		);

		return $safe_text;
	}
endif;
