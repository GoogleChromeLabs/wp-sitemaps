<?php

/**
 * Class Core_Sitemaps_Posts.
 * Builds the sitemap pages for Posts.
 */
class Core_Sitemaps_Posts extends Core_Sitemaps_Provider {
	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $object_type = 'post';

	/**
	 * Sitemap name
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	protected $name = 'posts';

	/**
	 * Bootstrapping the filters.
	 */
	public function bootstrap() {
		add_action( 'core_sitemaps_setup_sitemaps', array( $this, 'register_sitemap' ), 99 );
		add_action( 'template_redirect', array( $this, 'render_sitemap' ) );
	}

	/**
	 * Sets up rewrite rule for sitemap_index.
	 */
	public function register_sitemap() {
		$this->registry->add_sitemap( $this->name, '^sitemap-posts\.xml$', esc_url( $this->get_sitemap_url( $this->name ) ) );
	}

	/**
	 * Produce XML to output.
	 */
	public function render_sitemap() {
		$sitemap = get_query_var( 'sitemap' );
		$paged   = get_query_var( 'paged' );

		if ( 'posts' === $sitemap ) {
			$content  = $this->get_content_per_page( $this->object_type, $paged );
			$renderer = new Core_Sitemaps_Renderer();
			$renderer->render_urlset( $content, $this->object_type );
			exit;
		}
	}
}
