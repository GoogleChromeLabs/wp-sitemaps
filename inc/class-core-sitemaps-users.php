<?php
/**
 * The Core_Sitemaps_Users sitemap provider.
 *
 * This class extends Core_Sitemaps_Provider to support sitemaps for user pages in WordPress.
 *
 * @package Core_Sitemaps
 */

/**
 * Class Core_Sitemaps_Users
 */
class Core_Sitemaps_Users extends Core_Sitemaps_Provider {
	/**
	 * Core_Sitemaps_Users constructor.
	 */
	public function __construct() {
		$this->object_type = 'user';
		$this->name        = 'users';
		$this->route       = '^sitemap-users-?([0-9]+)?\.xml$';
		$this->slug        = 'users';
	}

	/**
	 * Get a URL list for a user sitemap.
	 *
	 * @param int $page_num Page of results.
	 *
	 * @return array $url_list List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num ) {
		$object_type       = $this->object_type;
		$public_post_types = get_post_types(
			array(
				'public' => true,
			)
		);

		// We're not supporting sitemaps for author pages for attachments.
		unset( $public_post_types['attachment'] );

		$query = new WP_User_Query(
			array(
				'has_published_posts' => array_keys( $public_post_types ),
				'number'              => CORE_SITEMAPS_POSTS_PER_PAGE,
				'paged'               => absint( $page_num ),
			)
		);

		$users = $query->get_results();

		$url_list = array();

		foreach ( $users as $user ) {
			$last_modified = get_posts(
				array(
					'author'        => $user->ID,
					'orderby'       => 'date',
					'numberposts'   => 1,
					'no_found_rows' => true,
				)
			);

			$url_list[] = array(
				'loc'     => get_author_posts_url( $user->ID ),
				'lastmod' => mysql2date( DATE_W3C, $last_modified[0]->post_modified_gmt, false ),
			);
		}

		/**
		 * Filter the list of URLs for a sitemap before rendering.
		 *
		 * @since 0.1.0
		 * @param string $object_type Name of the post_type.
		 * @param int    $page_num    Page of results.
		 *
		 * @param array  $url_list    List of URLs for a sitemap.
		 */
		return apply_filters( 'core_sitemaps_users_url_list', $url_list, $object_type, $page_num );
	}

	/**
	 * Produce XML to output.
	 *
	 * @noinspection PhpUnused
	 */
	public function render_sitemap() {
		$sitemap = get_query_var( 'sitemap' );
		$paged   = get_query_var( 'paged' );

		if ( empty( $paged ) ) {
			$paged = 1;
		}

		if ( 'users' === $sitemap ) {
			$url_list = $this->get_url_list( $paged );
			$renderer = new Core_Sitemaps_Renderer();
			$renderer->render_sitemap( $url_list );
			exit;
		}
	}
}
