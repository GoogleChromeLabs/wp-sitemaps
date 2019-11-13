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
	 * @param array                  $names    Names of the provider's sitemaps.
	 * @param Core_Sitemaps_Provider $provider Instance of a Core_Sitemaps_Provider.
	 * @return bool True if the sitemap was added, false if it wasn't as it's name was already registered.
	 */
	public function add_sitemap( $names, $provider ) {
		if ( ! $provider instanceof Core_Sitemaps_Provider ) {
			return false;
		}

		// Take multi-dimensional array of names and add the provider as the value for all.
		array_walk_recursive(
			$names,
			static function ( &$value, &$key, &$provider ) {
				$value = $provider;
			},
			$provider
		);

		// Add one or more sitemaps per provider.
		foreach ( $names as $object_name => $maybe_provider ) {
			if ( $maybe_provider instanceof Core_Sitemaps_Provider ) {
				if ( isset( $this->sitemaps[ $object_name ] ) ) {
					return false;
				}
				$this->sitemaps[ $object_name ] = $maybe_provider;

				return true;
			}

			foreach ( array_keys( $maybe_provider ) as $sub_name ) {
				if ( isset( $this->sitemaps[ $object_name ][ $sub_name ] ) ) {
					return false;
				}
			}
			$this->sitemaps = array_merge( $this->sitemaps, $names );

			return true;
		}

		// We shouldn't get to here.
		return false;
	}

	/**
	 * List of all registered sitemaps.
	 *
	 * @return array List of sitemaps.
	 */
	public function get_sitemaps() {
		$total_sitemaps = count( $this->sitemaps );

		if ( $total_sitemaps > CORE_SITEMAPS_MAX_URLS ) {
			return array_slice( $this->sitemaps, 0, CORE_SITEMAPS_MAX_URLS, true );
		}

		return $this->sitemaps;
	}
}
