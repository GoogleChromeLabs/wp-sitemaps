<?php
/**
 * Sitemaps: Core_Sitemaps_Registry class
 *
 * This class handles registration sitemaps.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since x.x.x
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
	 * @return bool True if the sitemap was added, false if it is already registered.
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
	 * Returns a single sitemap provider.
	 *
	 * @param string $name Sitemap provider name.
	 *
	 * @return Core_Sitemaps_Provider|null Provider if it exists, null otherwise.
	 */
	public function get_provider( $name ) {
		if ( ! isset( $this->sitemaps[ $name ] ) ) {
			return null;
		}

		return $this->sitemaps[ $name ];
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
