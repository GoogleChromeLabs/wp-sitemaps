<?php
/**
 * Core sitemap public functions.
 *
 * @package Core_Sitemaps
 */

/**
 * Get a list of URLs for all sitemaps.
 *
 * @return array $urls A list of sitemap URLs.
 */
function core_sitemaps_get_sitemaps() {
	global $core_sitemaps;

	$urls = $core_sitemaps->registry->get_sitemaps();

	return $urls;
}
