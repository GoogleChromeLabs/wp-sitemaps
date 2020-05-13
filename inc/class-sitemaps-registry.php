<?php
/**
 * Sitemaps: Sitemaps_Registry class
 *
 * Handles registering sitemaps.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Class Sitemaps_Registry.
 *
 * @since 5.5.0
 */
class Sitemaps_Registry {
	/**
	 * Registered sitemaps.
	 *
	 * @since 5.5.0
	 *
	 * @var array Array of registered sitemaps.
	 */
	private $sitemaps = array();

	/**
	 * Adds a sitemap with route to the registry.
	 *
	 * @since 5.5.0
	 *
	 * @param string            $name     Name of the sitemap.
	 * @param Sitemaps_Provider $provider Instance of a Sitemaps_Provider.
	 * @return bool True if the sitemap was added, false if it is already registered.
	 */
	public function add_sitemap( $name, $provider ) {
		if ( isset( $this->sitemaps[ $name ] ) ) {
			return false;
		}

		if ( ! $provider instanceof Sitemaps_Provider ) {
			return false;
		}

		$this->sitemaps[ $name ] = $provider;

		return true;
	}

	/**
	 * Returns a single registered sitemaps provider.
	 *
	 * @since 5.5.0
	 *
	 * @param string $name Sitemap provider name.
	 * @return Sitemaps_Provider|null Sitemaps provider if it exists, null otherwise.
	 */
	public function get_sitemap( $name ) {
		if ( ! isset( $this->sitemaps[ $name ] ) ) {
			return null;
		}

		return $this->sitemaps[ $name ];
	}

	/**
	 * Lists all registered sitemaps.
	 *
	 * @since 5.5.0
	 *
	 * @return array List of sitemaps.
	 */
	public function get_sitemaps() {
		$total_sitemaps = count( $this->sitemaps );

		if ( $total_sitemaps > SITEMAPS_MAX_SITEMAPS ) {
			return array_slice( $this->sitemaps, 0, SITEMAPS_MAX_SITEMAPS, true );
		}

		return $this->sitemaps;
	}
}
