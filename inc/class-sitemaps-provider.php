<?php

/**
 * Class Core_Sitemaps_Provider
 */
class Core_Sitemaps_Provider {
	/**
	 * Registry instance
	 *
	 * @var Core_Sitemaps_Registry
	 */
	public $registry;
	/**
	 * Post Type name
	 *
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * Core_Sitemaps_Provider constructor.
	 */
	public function __construct() {
		$this->registry = Core_Sitemaps_Registry::instance();
	}

	/**
	 * General renderer for Sitemap Provider instances.
	 *
	 * @param WP_Post[] $content List of WP_Post objects.
	 */
	public function render( $content ) {
		header( 'Content-type: application/xml; charset=UTF-8' );
		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		foreach ( $content as $post ) {
			$url_data = array(
				'loc'        => get_permalink( $post ),
				// DATE_W3C does not contain a timezone offset, so UTC date must be used.
				'lastmod'    => mysql2date( DATE_W3C, $post->post_modified_gmt, false ),
				'priority'   => '0.5',
				'changefreq' => 'monthly',
			);
			printf(
				'<url>
<loc>%1$s</loc>
<lastmod>%2$s</lastmod>
<changefreq>%3$s</changefreq>
<priority>%4$s</priority>
</url>',
				esc_html( $url_data['loc'] ),
				esc_html( $url_data['lastmod'] ),
				esc_html( $url_data['changefreq'] ),
				esc_html( $url_data['priority'] )
			);
		}
		echo '</urlset>';
	}

	/**
	 * Get content for a page.
	 *
	 * @param string $post_type Name of the post_type.
	 * @param int    $page_num Page of results.
	 *
	 * @return int[]|WP_Post[] Query result.
	 */
	public function get_content_per_page( $post_type, $page_num = 1 ) {
		$query = new WP_Query();

		return $query->query(
			array(
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'post_type'      => $post_type,
				'posts_per_page' => CORE_SITEMAPS_POSTS_PER_PAGE,
				'paged'          => $page_num,
			)
		);
	}

	/**
	 * Builds the URL for the sitemaps.
	 *
	 * @return string the sitemap index url.
	 */
	public function get_sitemap_url( $post_type ) {
		global $wp_rewrite;

		if ( $post_type === 'index' ) {
			$url = home_url( '/sitemap.xml' );

			if ( ! $wp_rewrite->using_permalinks() ) {
				$url = add_query_arg( 'sitemap', 'index', home_url( '/' ) );
			}
		} else {
			$url = home_url( sprintf( '/sitemap-%1$s.xml', $post_type ) );

			if ( ! $wp_rewrite->using_permalinks() ) {
				$url = add_query_arg( 'sitemap', $post_type, home_url( '/' ) );
			}
		}

		return $url;
	}
}
