<?php

/**
 * Core Sitemaps Registry
 *
 * @package Core_Sitemaps
 */
class Core_Sitemaps_Registry {
	/**
	 * Registered sitemaps.
	 *
	 * @var array Array of registered sitemaps.
	 */
	private $sitemaps = [];

	/**
	 * Add a sitemap with route to the registry.
	 *
	 * @param string                 $name Name of the sitemap.
	 * @param Core_Sitemaps_Provider $provider Instance of a Core_Sitemaps_Provider.
	 *
	 * @return bool True if the sitemap was added, false if it wasn't as it's name was already registered.
	 */
	public function add_sitemap( $name, $provider ) {
		if ( isset( $this->sitemaps[ $name ] ) ) {
			return false;
		}

		if ( ! is_a( $provider, 'Core_Sitemaps_Provider' ) ) {
			return false;
		}

		$this->sitemaps[ $name ] = $provider;

		return true;
	}

	/**
	 * Remove sitemap by name.
	 *
	 * @param string $name Sitemap name.
	 *
	 * @return array Remaining sitemaps.
	 */
	public function remove_sitemap( $name ) {
		unset( $this->sitemaps[ $name ] );

		return $this->sitemaps;
	}

	/**
	 * List of all registered sitemaps.
	 *
	 * @return array List of sitemaps.
	 */
	public function get_sitemaps() {
		$total_sitemaps = count( $this->sitemaps );

		if ( $total_sitemaps > CORE_SITEMAPS_MAX_URLS ) {
			$max_sitemaps = array_slice( $this->sitemaps, 0, CORE_SITEMAPS_MAX_URLS, true );

			return $max_sitemaps;
		} else {
			return $this->sitemaps;
		}
	}
}
