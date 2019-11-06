<?php

/**
 * Class Core_Sitemaps_Posts.
 * Builds the sitemap pages for Posts.
 */
class Core_Sitemaps_Pages extends Core_Sitemaps_Provider {
	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $post_type = 'page';
	/**
	 * Sitemap name
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	protected $name = 'pages';

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
		$this->registry->add_sitemap( $this->name, '^sitemap-pages\.xml$', esc_url( $this->get_sitemap_url( $this->name ) ) );
	}

	/**
	 * Produce XML to output.
	 */
	public function render_sitemap() {
		$sitemap = get_query_var( 'sitemap' );
		$paged   = get_query_var( 'paged' );

		if ( 'pages' === $sitemap ) {
			$content  = $this->get_content_per_page( $this->post_type, $paged );
			$renderer = new Core_Sitemaps_Renderer();
			$renderer->render_sitemap( $content );
			exit;
		}
	}
}
