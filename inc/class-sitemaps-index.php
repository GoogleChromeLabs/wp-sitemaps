<?php
/**
 * Class Core_Sitemaps_Index.
 * Builds the sitemap index page that lists the links to all of the sitemaps.
 *
 */
class Core_Sitemaps_Index extends Core_Sitemaps_Provider {
	/**
	 * Sitemap name
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	protected $name = 'index';

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
		$this->registry->add_sitemap( $this->name, 'sitemap\.xml$', esc_url( $this->get_sitemap_url( $this->name ) ) );
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
	 * Add the correct xml to any given url.
	 *
	 * @todo This will also need to be updated with the last modified information as well.
	 *
	 * @return string $markup
	 */
	public function get_index_url_markup( $url ) {
		$markup = '<sitemap>' . "\n";
		$markup .= '<loc>' . esc_url( $url ) . '</loc>' . "\n";
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
		$sitemaps_urls = $this->registry->get_sitemaps();

		if ( 'index' === $sitemap_index ) {
			header( 'Content-type: application/xml; charset=UTF-8' );

			echo '<?xml version="1.0" encoding="UTF-8" ?>';
			echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

			foreach ( $sitemaps_urls as $link ) {
				echo $this->get_index_url_markup( $link['slug'] );
			}

			echo '</sitemapindex>';
			exit;
		}
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
			$output .= 'Sitemap: ' . esc_url( $this->get_sitemap_url( $this->name ) ) . "\n";
		}
		return $output;
	}
}
