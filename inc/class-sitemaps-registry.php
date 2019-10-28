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

	public function __construct() {
		// Nothing happening
	}

	public function add_sitemap( $name, $route, $args = [] ) {
		if ( isset( $this->sitemaps[ $name ] ) ) {
			return false;
		}

		$this->sitemaps[ $name ] = [
			'route' => $route,
			'args'  => $args,
		];
	}

	public function remove_sitemap( $name ) {
		unset( $this->sitemaps[ $name ] );

		return $this->sitemaps;
	}

	public function get_sitemaps() {
		return $this->sitemaps;
	}

	/**
	 * Setup rewrite rules for all registered sitemaps.
	 *
	 * @return void
	 */
	public function setup_sitemaps() {
		do_action( 'core_sitemaps_setup_sitemaps' );

		foreach ( $this->sitemaps as $sitemap ) {
			add_rewrite_rule( $sitemap->route, 'index.php?sitemap=' . $sitemap->name, 'top' );
		}
	}
}
