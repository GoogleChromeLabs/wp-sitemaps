<?php
/**
 * Class Core_Sitemaps_Index.
 * Builds the sitemap index page that lists the links to all of the sitemaps.
 *
 */
class Core_Sitemaps_Index {
	/**
	 * @var Core_Sitemaps_Registry object
	 */
	public $registry;

	/**
	 * Core_Sitemaps_Index constructor.
	 */
	public function __construct() {
		$this->registry = Core_Sitemaps_Registry::instance();
	}

	/**
	 *
	 * A helper function to initiate actions, hooks and other features needed.
	 *
	 * @uses add_action()
	 * @uses add_filter()
	 */
	public function bootstrap() {
		add_action( 'core_sitemaps_setup_sitemaps', array( $this, 'register_sitemap' ), 99 );
		add_filter( 'robots_txt', array( $this, 'add_robots' ), 0, 2 );
		add_filter( 'redirect_canonical', array( $this, 'redirect_canonical' ) );
		add_action( 'template_redirect', array( $this, 'render_sitemap' ) );

		// FIXME: Move this into a Core_Sitemaps class registration system.
		$core_sitemaps_posts = new Core_Sitemaps_Posts();
		$core_sitemaps_posts->bootstrap();
	}

	/**
	 * Sets up rewrite rule for sitemap_index.
	 */
	public function register_sitemap() {
		$this->registry->add_sitemap( 'sitemap_index', 'sitemap\.xml$' );
	}

	/**
	 * Prevent trailing slashes.
	 *
	 * @param string $redirect The redirect URL currently determined.
	 * @return bool|string $redirect
	 */
	public function redirect_canonical( $redirect ) {
		if ( get_query_var( 'sitemap' ) ) {
			return false;
		}

		return $redirect;
	}

	/**
	 * Get all of the available sitemaps from the registry
	 * and add to an array.
	 *
	 * @todo get_registered_sitemaps() and get_sitemap_urls() are looping through teh array and
	 * nested array to get at the value for ['route']. There is probably a better way to do
	 * this than two methods that are almost identical.
	 *
	 * @return array $sitemaps_list
	 */
	public function get_registered_sitemaps() {
		$sitemaps_list = array();
		$sitemaps_all = $this->registry->get_sitemaps();

		foreach ( $sitemaps_all as $sitemaps ) {
			array_push( $sitemaps_list, $sitemaps );
		}

		return $sitemaps_list;
	}

	/**
	 * Get all of the URLs for the sitemaps and add to an array.
	 *
	 * @return array $sitemaps_urls
	 */
	public function get_sitemap_urls() {
		$sitemap_urls = array();
		$sitemaps_list = $this->get_registered_sitemaps();

		foreach ( $sitemaps_list as $sitemap ) {
			array_push( $sitemap_urls, $sitemap );
		}

		return $sitemap_urls;
	}

	/**
	 * Add the correct xml to any given url.
	 *
	 * @todo This will also need to be updated with the last modified information as well.
	 *
	 * @return string $markup
	 */
	public function get_index_url_markup( $url ) {
		$markup = '<sitemap>' . "\n";
		$markup .= '<loc>' . $url . '</loc>' . "\n";
		$markup .= '<lastmod>2004-10-01T18:23:17+00:00</lastmod>' . "\n";
		$markup .= '</sitemap>' . "\n";

		return $markup;
	}

	/**
	 * Produce XML to output.
	 *
	 * @todo At the moment this outputs the rewrite rule for each sitemap rather than the URL.
	 * This will need changing.
	 *
	 */
	public function render_sitemap() {
		$sitemap_index = get_query_var( 'sitemap' );
		$sitemap_urls = $this->get_sitemap_urls();

		if ( 'sitemap_index' === $sitemap_index ) {
			header( 'Content-type: application/xml; charset=UTF-8' );

			echo '<?xml version="1.0" encoding="UTF-8" ?>';
			echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

			foreach ( $sitemap_urls as $link ) {
				echo $this->get_index_url_markup( $link['route'] );
			}

			echo '</sitemapindex>';
			exit;
		}
	}

	/**
	 * Builds the URL for the sitemap index.
	 *
	 * @return string the sitemap index url.
	 */
	public function sitemap_index_url() {
		global $wp_rewrite;

		$url = home_url( '/sitemap.xml' );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$url = add_query_arg( 'sitemap', 'sitemap_index', home_url( '/' ) );
		}

		return $url;
	}

	/**
	 * Adds the sitemap index to robots.txt.
	 *
	 * @param string $output robots.txt output.
	 * @param bool   $public Whether the site is public or not.
	 * @return string robots.txt output.
	 */
	public function add_robots( $output, $public ) {
		if ( $public ) {
			$output .= 'Sitemap: ' . esc_url( $this->sitemap_index_url() ) . "\n";
		}
		return $output;
	}
}
