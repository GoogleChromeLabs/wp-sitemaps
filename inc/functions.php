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
 * @return Core_Sitemaps|null Core_Sitemaps instance, or null of sitemaps are disabled.
 */
function core_sitemaps_get_server() {
	/**
	 * Global Core Sitemaps instance.
	 *
	 * @since 5.5.0
	 *
	 * @var Core_Sitemaps $core_sitemaps
	 */
	global $core_sitemaps;

	$is_enabled = (bool) get_option( 'blog_public' );

	/**
	 * Filters whether XML Sitemaps are enabled or not.
	 *
	 * @since 5.5.0
	 *
	 * @param bool $is_enabled Whether XML Sitemaps are enabled or not. Defaults to true for public sites.
	 */
	$is_enabled = (bool) apply_filters( 'core_sitemaps_is_enabled', $is_enabled );

	if ( ! $is_enabled ) {
		return null;
	}

	// If there isn't a global instance, set and bootstrap the sitemaps system.
	if ( empty( $core_sitemaps ) ) {
		$core_sitemaps = new Core_Sitemaps();
		$core_sitemaps->init();

		/**
		 * Fires when initializing the Core_Sitemaps object.
		 *
		 * Additional sitemaps should be registered on this hook.
		 *
		 * @since 5.5.0
		 *
		 * @param core_sitemaps $core_sitemaps Server object.
		 */
		do_action( 'core_sitemaps_init', $core_sitemaps );
	}

	return $core_sitemaps;
}

/**
 * Gets a list of sitemap providers.
 *
 * @since 5.5.0
 *
 * @return array $sitemaps A list of registered sitemap providers.
 */
function core_sitemaps_get_sitemaps() {
	$core_sitemaps = core_sitemaps_get_server();

	if ( ! $core_sitemaps ) {
		return array();
	}

	return $core_sitemaps->registry->get_sitemaps();
}

/**
 * Registers a new sitemap provider.
 *
 * @since 5.5.0
 *
 * @param string                 $name     Unique name for the sitemap provider.
 * @param Core_Sitemaps_Provider $provider The `Core_Sitemaps_Provider` instance implementing the sitemap.
 * @return bool Returns true if the sitemap was added. False on failure.
 */
function core_sitemaps_register_sitemap( $name, $provider ) {
	$core_sitemaps = core_sitemaps_get_server();

	if ( ! $core_sitemaps ) {
		return false;
	}

	return $core_sitemaps->registry->add_sitemap( $name, $provider );
}

/**
 * Gets the maximum number of URLs for a sitemap.
 *
 * @since 5.5.0
 *
 * @param string $object_type Object type for sitemap to be filtered (e.g. 'post', 'term', 'user').
 * @return int The maximum number of URLs.
 */
function core_sitemaps_get_max_urls( $object_type ) {
	/**
	 * Filters the maximum number of URLs displayed on a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param int    $max_urls    The maximum number of URLs included in a sitemap. Default 2000.
	 * @param string $object_type Object type for sitemap to be filtered (e.g. 'post', 'term', 'user').
	 */
	return apply_filters( 'core_sitemaps_max_urls', CORE_SITEMAPS_MAX_URLS, $object_type );
}
