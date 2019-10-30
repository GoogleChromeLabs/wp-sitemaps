<?php

/**
 * Class Core_Sitemaps_Posts.
 * Builds the sitemap pages for Posts.
 */
class Core_Sitemaps_Posts {
	/**
	 * Content of the sitemap to output.
	 *
	 * @var array
	 */
	protected $content = [];
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
	 * Bootstrapping the filters.
	 */
	public function bootstrap() {
		add_action( 'core_sitemaps_setup_sitemaps', array( $this, 'register_sitemap' ), 99 );
		add_filter( 'template_include', array( $this, 'template' ) );
	}

	/**
	 * Sets up rewrite rule for sitemap_index.
	 */
	public function register_sitemap() {
		$this->registry->add_sitemap( 'posts', '^sitemap-posts\.xml$' );
	}

	/**
	 * Produce XML to output.
	 *
	 * @param string $template The template to return. Either custom XML or default.
	 *
	 * @return string
	 */
	public function template( $template ) {
		$sitemap = get_query_var( 'sitemap' );
		$paged   = get_query_var( 'paged' );

		if ( 'posts' !== $sitemap ) {
			return $template;
		}

		$this->content = $this->get_content_per_page( $paged );

		header( 'Content-type: application/xml; charset=UTF-8' );
		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		foreach ( $this->content as $post ) {
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
		exit;
	}

	/**
	 * Get content for a page.
	 *
	 * @param int $page_num Page of results.
	 *
	 * @return int[]|WP_Post[] Query result.
	 */
	public function get_content_per_page( $page_num = 1 ) {
		$query = new WP_Query();

		return $query->query(
			array(
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'post_type'      => 'post',
				'posts_per_page' => CORE_SITEMAPS_POSTS_PER_PAGE,
				'paged'          => $page_num,
			)
		);
	}
}
