<?php
/**
 * Sitemaps: WP_Sitemaps class
 *
 * This is the main class integrating all other classes.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Class WP_Sitemaps.
 *
 * @since 5.5.0
 */
class WP_Sitemaps {
	/**
	 * The main index of supported sitemaps.
	 *
	 * @since 5.5.0
	 *
	 * @var WP_Sitemaps_Index
	 */
	public $index;

	/**
	 * The main registry of supported sitemaps.
	 *
	 * @since 5.5.0
	 *
	 * @var WP_Sitemaps_Registry
	 */
	public $registry;

	/**
	 * An instance of the renderer class.
	 *
	 * @since 5.5.0
	 *
	 * @var WP_Sitemaps_Renderer
	 */
	public $renderer;

	/**
	 * WP_Sitemaps constructor.
	 *
	 * @since 5.5.0
	 */
	public function __construct() {
		$this->registry = new WP_Sitemaps_Registry();
		$this->renderer = new WP_Sitemaps_Renderer();
		$this->index    = new WP_Sitemaps_Index( $this->registry );
	}

	/**
	 * Initiates all sitemap functionality.
	 *
	 * @since 5.5.0
	 */
	public function init() {
		// These will all fire on the init hook.
		$this->register_sitemaps();

		// Add additional action callbacks.
		add_action( 'wp_sitemaps_init', array( $this, 'register_rewrites' ) );
		add_action( 'template_redirect', array( $this, 'render_sitemaps' ) );
		add_action( 'wp_loaded', array( $this, 'maybe_flush_rewrites' ) );
		add_filter( 'pre_handle_404', array( $this, 'redirect_sitemapxml' ), 10, 2 );
		add_filter( 'robots_txt', array( $this, 'add_robots' ), 0, 2 );
		add_filter( 'redirect_canonical', array( $this, 'redirect_canonical' ) );
	}

	/**
	 * Registers and sets up the functionality for all supported sitemaps.
	 *
	 * @since 5.5.0
	 */
	public function register_sitemaps() {
		/**
		 * Filters the list of registered sitemap providers.
		 *
		 * @since 5.5.0
		 *
		 * @param array $providers {
		 *     Array of WP_Sitemaps_Provider objects keyed by their name.
		 *
		 *     @type object $posts      The WP_Sitemaps_Posts object.
		 *     @type object $taxonomies The WP_Sitemaps_Taxonomies object.
		 *     @type object $users      The WP_Sitemaps_Users object.
		 * }
		 */
		$providers = apply_filters(
			'wp_sitemaps_register_providers',
			array(
				'posts'      => new WP_Sitemaps_Posts(),
				'taxonomies' => new WP_Sitemaps_Taxonomies(),
				'users'      => new WP_Sitemaps_Users(),
			)
		);

		// Register each supported provider.
		/* @var WP_Sitemaps_Provider $provider */
		foreach ( $providers as $name => $provider ) {
			$this->registry->add_sitemap( $name, $provider );
		}
	}

	/**
	 * Registers sitemap rewrite tags and routing rules.
	 *
	 * @since 5.5.0
	 */
	public function register_rewrites() {
		// Add rewrite tags.
		add_rewrite_tag( '%sitemap%', '([^?]+)' );
		add_rewrite_tag( '%sitemap-sub-type%', '([^?]+)' );

		// Register index route.
		add_rewrite_rule( '^wp-sitemap\.xml$', 'index.php?sitemap=index', 'top' );

		// Register rewrites for the XSL stylesheet.
		add_rewrite_tag( '%sitemap-stylesheet%', '([^?]+)' );
		add_rewrite_rule( '^wp-sitemap\.xsl$', 'index.php?sitemap-stylesheet=sitemap', 'top' );
		add_rewrite_rule( '^wp-sitemap-index\.xsl$', 'index.php?sitemap-stylesheet=index', 'top' );

		// Register routes for providers.
		add_rewrite_rule(
			'^wp-sitemap-([a-z]+?)-([a-z\d_-]+?)-(\d+?)\.xml$',
			'index.php?sitemap=$matches[1]&sitemap-sub-type=$matches[2]&paged=$matches[3]',
			'top'
		);
		add_rewrite_rule(
			'^wp-sitemap-([a-z]+?)-(\d+?)\.xml$',
			'index.php?sitemap=$matches[1]&paged=$matches[2]',
			'top'
		);
	}

	/**
	 * Un-registers sitemap rewrite tags and routing rules.
	 *
	 * @since 5.5.0
	 */
	public function unregister_rewrites() {
		/* @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;

		// Unregister index route.
		unset( $wp_rewrite->extra_rules_top['^wp-sitemap\.xml$'] );

		// Unregister rewrites for the XSL stylesheet.
		unset( $wp_rewrite->extra_rules_top['^wp-sitemap\.xsl$'] );
		unset( $wp_rewrite->extra_rules_top['^wp-sitemap-index\.xsl$'] );

		// Unregister routes for providers.
		unset( $wp_rewrite->extra_rules_top['^wp-sitemap-([a-z]+?)-([a-z\d-]+?)-(\d+?)\.xml$'] );
		unset( $wp_rewrite->extra_rules_top['^wp-sitemap-([a-z]+?)-(\d+?)\.xml$'] );
	}

	/**
	 * Flushes rewrite rules if developers updated them.
	 *
	 * @since 5.5.0
	 */
	public function maybe_flush_rewrites() {
		if ( update_option( 'wp_sitemaps_rewrite_version', WP_SITEMAPS_REWRITE_VERSION ) ) {
			flush_rewrite_rules( false );
		}
	}

	/**
	 * Renders sitemap templates based on rewrite rules.
	 *
	 * @since 5.5.0
	 */
	public function render_sitemaps() {
		global $wp_query;

		$sitemap         = sanitize_text_field( get_query_var( 'sitemap' ) );
		$object_subtype  = sanitize_text_field( get_query_var( 'sitemap-sub-type' ) );
		$stylesheet_type = sanitize_text_field( get_query_var( 'sitemap-stylesheet' ) );
		$paged           = absint( get_query_var( 'paged' ) );

		// Bail early if this isn't a sitemap or stylesheet route.
		if ( ! ( $sitemap || $stylesheet_type ) ) {
			return;
		}

		// Render stylesheet if this is stylesheet route.
		if ( $stylesheet_type ) {
			$stylesheet = new WP_Sitemaps_Stylesheet();

			$stylesheet->render_stylesheet( $stylesheet_type );
			exit;
		}

		// Render the index.
		if ( 'index' === $sitemap ) {
			$sitemap_list = $this->index->get_sitemap_list();

			$this->renderer->render_index( $sitemap_list );
			exit;
		}

		$provider = $this->registry->get_sitemap( $sitemap );

		if ( ! $provider ) {
			return;
		}

		if ( empty( $paged ) ) {
			$paged = 1;
		}

		$url_list = $provider->get_url_list( $paged, $object_subtype );

		// Force a 404 and bail early if no URLs are present.
		if ( empty( $url_list ) ) {
			$wp_query->set_404();
			return;
		}

		$this->renderer->render_sitemap( $url_list );
		exit;
	}

	/**
	 * Redirects a URL to the wp-sitemap.xml
	 *
	 * @since 5.5.0
	 *
	 * @param bool     $bypass Pass-through of the pre_handle_404 filter value.
	 * @param WP_Query $query The WP_Query object.
	 * @return bool Bypass value.
	 */
	public function redirect_sitemapxml( $bypass, $query ) {
		// If a plugin has already utilized the pre_handle_404 function, return without action to avoid conflicts.
		if ( $bypass ) {
			return $bypass;
		}

		// 'pagename' is for most permalink types, name is for when the %postname% is used as a top-level field.
		if ( 'sitemap-xml' === $query->get( 'pagename' ) ||
			 'sitemap-xml' === $query->get( 'name' ) ) {
			wp_safe_redirect( $this->index->get_index_url() );
			exit();
		}

		return $bypass;
	}

	/**
	 * Adds the sitemap index to robots.txt.
	 *
	 * @since 5.5.0
	 *
	 * @param string $output robots.txt output.
	 * @param bool   $public Whether the site is public or not.
	 * @return string The robots.txt output.
	 */
	public function add_robots( $output, $public ) {
		if ( $public ) {
			$output .= "\nSitemap: " . esc_url( $this->index->get_index_url() ) . "\n";
		}

		return $output;
	}

	/**
	 * Prevent trailing slashes.
	 *
	 * @since 5.5.0
	 *
	 * @param string $redirect The redirect URL currently determined.
	 * @return bool|string $redirect The canonical redirect URL.
	 */
	public function redirect_canonical( $redirect ) {
		if ( get_query_var( 'sitemap' ) || get_query_var( 'sitemap-stylesheet' ) ) {
			return false;
		}

		return $redirect;
	}
}
