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
	 * Core_Sitemaps_Registry constructor.
	 *  Setup all registered sitemap data providers, after all are registered at priority 99.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'setup_sitemaps' ), 100 );
	}

	/**
	 * Returns the *Singleton* instance of this class.
	 * FIXME: Instantiate a single class of this in a future Core_Sitemaps class.
	 *
	 * @staticvar Singleton $instance The *Singleton* instances of this class.
	 *
	 * @return self
	 */
	public static function instance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Add a sitemap with route to the registry.
	 *
	 * @param string $name Name of the sitemap.
	 * @param string $route Regex route of the sitemap.
	 * @param array  $args List of other arguments.
	 *
	 * @return bool True if the sitemap was added, false if it wasn't as it's name was already registered.
	 */
	public function add_sitemap( $name, $route, $args = [] ) {
		if ( isset( $this->sitemaps[ $name ] ) ) {
			return false;
		}

		$this->sitemaps[ $name ] = [
			'route' => $route,
			'args'  => $args,
		];

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

		if ( $total_sitemaps <= CORE_SITEMAPS_INDEX_MAX ) {
			return $this->sitemaps;
		}
	}

	/**
	 * Setup rewrite rules for all registered sitemaps.
	 *
	 * @return void
	 */
	public function setup_sitemaps() {
		do_action( 'core_sitemaps_setup_sitemaps' );

		foreach ( $this->sitemaps as $name => $sitemap ) {
			add_rewrite_tag( '%sitemap%', $name );
			add_rewrite_rule( $sitemap['route'], 'index.php?sitemap=' . $name, 'top' );
		}
	}
}
