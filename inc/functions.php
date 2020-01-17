<?php
/**
 * Core sitemap public functions.
 *
 * @package Core_Sitemaps
 */

/**
 * Get a list of sitemaps.
 *
 * @return array $sitemaps A list of registered sitemap providers.
 */
function core_sitemaps_get_sitemaps() {
	global $core_sitemaps;

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
	global $core_sitemaps;

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
