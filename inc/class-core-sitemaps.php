<?php
/**
 * Class file for the Core_Sitemaps class.
 *
 * This is the main class integrating all other classes.
 *
 * @package Core_Sitemaps
 */

/**
 * Class Core_Sitemaps
 */
class Core_Sitemaps {
	/**
	 * The main index of supported sitemaps.
	 *
	 * @var Core_Sitemaps_Index
	 */
	public $index;

	/**
	 * The main registry of supported sitemaps.
	 *
	 * @var Core_Sitemaps_Registry
	 */
	public $registry;

	/**
	 * Core_Sitemaps constructor.
	 */
	public function __construct() {
		$this->index    = new Core_Sitemaps_Index();
		$this->registry = new Core_Sitemaps_Registry();
	}

	/**
	 * Initiate all sitemap functionality.
	 *
	 * @return void
	 */
	public function bootstrap() {
		add_action( 'init', array( $this, 'setup_sitemaps_index' ) );
		add_action( 'init', array( $this, 'register_sitemaps' ) );
		add_action( 'init', array( $this, 'setup_sitemaps' ) );
		add_action( 'init', array( $this, 'xsl_stylesheet_rewrite' ) );
		add_action( 'init', array( $this, 'index_xsl_stylesheet_rewrite' ) );
		add_action( 'wp_loaded', array( $this, 'maybe_flush_rewrites' ) );
	}

	/**
	 * Set up the main sitemap index.
	 */
	public function setup_sitemaps_index() {
		$this->index->setup_sitemap();
	}

	/**
	 * Register and set up the functionality for all supported sitemaps.
	 */
	public function register_sitemaps() {
		/**
		 * Filters the list of registered sitemap providers.
		 *
		 * @since 0.1.0
		 *
		 * @param array $providers Array of Core_Sitemap_Provider objects.
		 */
		$providers = apply_filters(
			'core_sitemaps_register_providers',
			array(
				'posts'      => new Core_Sitemaps_Posts(),
				'taxonomies' => new Core_Sitemaps_Taxonomies(),
				'users'      => new Core_Sitemaps_Users(),
			)
		);

		// Register each supported provider.
		foreach ( $providers as $provider ) {
			$sitemaps = $provider->get_sitemaps();
			foreach ( $sitemaps as $sitemap ) {
				$this->registry->add_sitemap( $sitemap, $provider );
			}
		}
	}

	/**
	 * Register and set up the functionality for all supported sitemaps.
	 */
	public function setup_sitemaps() {
		add_rewrite_tag( '%sub_type%', '([^?]+)' );
		// Set up rewrites and rendering callbacks for each supported sitemap.
		foreach ( $this->registry->get_sitemaps() as $sitemap ) {
			if ( ! $sitemap instanceof Core_Sitemaps_Provider ) {
				return;
			}
			add_rewrite_rule( $sitemap->route, $sitemap->rewrite_query(), 'top' );
			add_action( 'template_redirect', array( $sitemap, 'render_sitemap' ) );
		}
	}

	/**
	 * Provide rewrite for the xsl stylesheet.
	 */
	public function xsl_stylesheet_rewrite() {
		add_rewrite_tag( '%stylesheet%', '([^?]+)' );
		add_rewrite_rule( '^sitemap\.xsl$', 'index.php?stylesheet=xsl', 'top' );

		$stylesheet = new Core_Sitemaps_Stylesheet();
		add_action( 'template_redirect', array( $stylesheet, 'render_stylesheet' ) );
	}

	/**
	 * Provide rewrite for the sitemap index xsl stylesheet.
	 */
	public function index_xsl_stylesheet_rewrite() {
		add_rewrite_rule( '^sitemap-index\.xsl$', 'index.php?stylesheet=index', 'top' );

		$stylesheet = new Core_Sitemaps_Stylesheet();
		add_action( 'template_redirect', array( $stylesheet, 'render_stylesheet' ) );
	}

	/**
	 * Flush rewrite rules if developers updated them.
	 */
	public function maybe_flush_rewrites() {
		if ( update_option( 'core_sitemaps_rewrite_version', CORE_SITEMAPS_REWRITE_VERSION ) ) {
			flush_rewrite_rules( false );
		}
	}
}
