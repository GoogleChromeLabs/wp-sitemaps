<?php
/**
 * Sitemaps: Public functions
 *
 * This file cocntains a variety of public functions developers can use to interact with
 * the XML Sitemaps API.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since x.x.x
 */

/**
 * Retrieves the current Sitemaps server instance.
 *
 * @return Core_Sitemaps Core_Sitemaps instance.
 */
function core_sitemaps_get_server() {
	/**
	 * Global Core Sitemaps instance.
	 *
	 * @var Core_Sitemaps $core_sitemaps
	 */
	global $core_sitemaps;

	// If there isn't a global instance, set and bootstrap the sitemaps system.
	if ( empty( $core_sitemaps ) ) {
		$core_sitemaps = new Core_Sitemaps();
		$core_sitemaps->init();

		/**
		 * Fires when initializing the Core_Sitemaps object.
		 *
		 * Additional sitemaps should be registered on this hook.
		 *
		 * @since 0.1.0
		 *
		 * @param core_sitemaps $core_sitemaps Server object.
		 */
		do_action( 'core_sitemaps_init', $core_sitemaps );
	}

	return $core_sitemaps;
}

/**
 * Get a list of sitemaps.
 *
 * @return array $sitemaps A list of registered sitemap providers.
 */
function core_sitemaps_get_sitemaps() {
	$core_sitemaps = core_sitemaps_get_server();

	return $core_sitemaps->registry->get_sitemaps();
}

/**
 * Register a new sitemap provider.
 *
 * @param string                 $name     Unique name for the sitemap provider.
 * @param Core_Sitemaps_Provider $provider The `Core_Sitemaps_Provider` instance implementing the sitemap.
 * @return bool Returns true if the sitemap was added. False on failure.
 */
function core_sitemaps_register_sitemap( $name, $provider ) {
	$core_sitemaps = core_sitemaps_get_server();

	return $core_sitemaps->registry->add_sitemap( $name, $provider );
}

/**
 * Get the maximum number of URLs for a sitemap.
 *
 * @since 0.1.0
 *
 * @param string $type Optional. The type of sitemap to be filtered. Default ''.
 * @return int The maximum number of URLs.
 */
function core_sitemaps_get_max_urls( $type = '' ) {
	/**
	 * Filter the maximum number of URLs displayed on a sitemap.
	 *
	 * @since 0.1.0
	 *
	 * @param int    $max_urls The maximum number of URLs included in a sitemap. Default 2000.
	 * @param string $type     Optional. The type of sitemap to be filtered. Default ''.
	 * @return int The maximum number of URLs.
	 */
	return apply_filters( 'core_sitemaps_max_urls', CORE_SITEMAPS_MAX_URLS, $type );
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
		$safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
		$safe_text = html_entity_decode( $safe_text, ENT_HTML5 );
		/**
		 * Filters a string cleaned and escaped for output in XML.
		 *
		 * Text passed to esc_xml() is stripped of invalid or special characters
		 * before output.  HTML named character references are converted to the
		 * equiablent code points.
		 *
		 * @since 5.5.0
		 *
		 * @param string $safe_text The text after it has been escaped.
		 * @param string $text      The text prior to being escaped.
		 */
		return apply_filters( 'esc_xml', $safe_text, $text ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
	}
endif;

if ( ! function_exists( 'esc_xml__' ) ) :
	/**
	 * Retrieve the translation of $text and escapes it for safe use in XML output.
	 *
	 * If there is no translation, or the text domain isn't loaded, the original text
	 * is escaped and returned.
	 *
	 * @since 5.5.0
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                       Default 'default'.
	 * @return string Translated text.
	 */
	function esc_xml__( $text, $domain = 'default' ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
		return esc_xml( translate( $text, $domain ) ); // phpcs:ignore WordPress.WP.I18n
	}
endif;
