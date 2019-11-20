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

	$sitemaps = $core_sitemaps->registry->get_sitemaps();

	return $sitemaps;
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
