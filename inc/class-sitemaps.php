<?php
/**
 * Class file for the Core_Sitemaps class.
 * This is the main class integrating all other classes.
 *
 * @package Core_Sitemaps
 */

/**
 * Class Core_Sitemaps
 */
class Core_Sitemaps {
	/**
	 * Core_Sitemaps constructor.
	 * Register the registry and bootstrap registered providers.
	 *
	 * @uses apply_filters
	 */
	public function __construct() {
		$registry = new Core_Sitemaps_Registry();
		/**
		 * Provides a 'core_sitemaps_register_providers' filter which contains a associated array of
		 * Core_Sitemap_Provider instances to register, with the key passed into it's bootstrap($key) function.
		 */
		$providers = apply_filters( 'core_sitemaps_register_providers', [] );

		foreach ( $providers as $key => $provider ) {
			if ( $provider instanceof Core_Sitemaps_Provider ) {
				$provider->set_registry( $registry );
				$provider->bootstrap( $key );
			}
		}
	}
}
