<?php

/**
 * Class Core_Sitemaps_Categories.
 * Builds the sitemap pages for Categories.
 */
class Core_Sitemaps_Categories extends Core_Sitemaps_Provider {
	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $object_type = 'category';
	/**
	 * Sitemap name
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	protected $name = 'categories';

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
		$this->registry->add_sitemap( $this->name, '^sitemap-categories\.xml$', esc_url( $this->get_sitemap_url( $this->name ) ) );
	}

	/**
	 * List the available terms.
	 */
	public function get_terms() {
		return $terms = get_terms( [
			'taxonomy' => $object_type
		] );
	}

	/**
	 * Produce XML to output.
	 */
	public function render_sitemap() {
		$sitemap = get_query_var( 'sitemap' );
		$paged   = get_query_var( 'paged' );

		if ( 'categories' === $sitemap ) {
			$terms = $this->get_terms();

			foreach ( $terms as $term ) {
				$content = $this->get_latest_posts_per_terms( $term );
				$renderer = new Core_Sitemaps_Renderer();
				$renderer->render_urlset( $content, $this->object_type, $term );
			}

			exit;
		}
	}
}
