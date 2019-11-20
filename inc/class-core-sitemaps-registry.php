<?php
/**
 * Core Sitemaps Registry
 *
 * @package Core_Sitemaps
 */

/**
 * Class Core_Sitemaps_Registry
 */
class Core_Sitemaps_Registry {
	/**
	 * Registered sitemaps.
	 *
	 * @var array Array of registered sitemaps.
	 */
	private $sitemaps = array();

	/**
	 * Add a sitemap with route to the registry.
	 *
	 * @param string                 $name     Name of the sitemap.
	 * @param Core_Sitemaps_Provider $provider Instance of a Core_Sitemaps_Provider.
	 * @return bool True if the sitemap was added, false if it wasn't as it's name was already registered.
	 */
	public function add_sitemap( $name, $provider ) {
		if ( isset( $this->sitemaps[ $name ] ) ) {
			return false;
		}

		if ( ! $provider instanceof Core_Sitemaps_Provider ) {
			return false;
		}

		$this->sitemaps[ $name ] = $provider;

		return true;
	}

	/**
	 * List of all registered sitemaps.
	 *
	 * @return array List of sitemaps.
	 */
	public function get_sitemaps() {
		$total_sitemaps = count( $this->sitemaps );

		if ( $total_sitemaps > CORE_SITEMAPS_MAX_SITEMAPS ) {
			return array_slice( $this->sitemaps, 0, CORE_SITEMAPS_MAX_SITEMAPS, true );
		}

		return $this->sitemaps;
	}
}
