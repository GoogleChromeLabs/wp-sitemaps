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
