<?php

/**
 * Register included providers.
 *
 * @param Core_Sitemaps_Provider[] $providers List of registered providers.
 *
 * @return Core_Sitemaps_Provider[] Updated list.
 */
function core_sitemaps_registration( $providers ) {
	$providers['sitemap-index'] = new Core_Sitemaps_Index();
	$providers['sitemap-posts'] = new Core_Sitemaps_Posts();
	$providers['sitemap-pages'] = new Core_Sitemaps_Pages();
	$providers['sitemap-categories'] = new Core_Sitemaps_Categories();

	return $providers;
}

add_filter( 'core_sitemaps_register_providers', 'core_sitemaps_registration' );
