<?php
/**
 * Sitemaps: Core_Sitemaps_Users class
 *
 * Builds the sitemaps for the 'user' object type.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Users XML sitemap provider.
 *
 * @since 5.5.0
 */
class Core_Sitemaps_Users extends Core_Sitemaps_Provider {
	/**
	 * Core_Sitemaps_Users constructor.
	 *
	 * @since 5.5.0
	 */
	public function __construct() {
		$this->name        = 'users';
		$this->object_type = 'user';
	}

	/**
	 * Gets a URL list for a user sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param int    $page_num       Page of results.
	 * @param string $object_subtype Optional. Not applicable for Users but
	 *                               required for compatibility with the parent
	 *                               provider class. Default empty.
	 * @return array List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num, $object_subtype = '' ) {
		$query    = $this->get_public_post_authors_query( $page_num );
		$users    = $query->get_results();
		$url_list = array();

		foreach ( $users as $user ) {
			$url_list[] = array(
				'loc'     => get_author_posts_url( $user->ID ),
			);
		}

		/**
		 * Filters the list of URLs for a sitemap before rendering.
		 *
		 * @since 5.5.0
		 *
		 * @param array  $url_list List of URLs for a sitemap.
		 * @param int    $page_num Page of results.
		 */
		return apply_filters( 'core_sitemaps_users_url_list', $url_list, $page_num );
	}

	/**
	 * Gets the max number of pages available for the object type.
	 *
	 * @since 5.5.0
	 *
	 * @see Core_Sitemaps_Provider::max_num_pages
	 *
	 * @param string $object_subtype Optional. Not applicable for Users but
	 *                               required for compatibility with the parent
	 *                               provider class. Default empty.
	 * @return int Total page count.
	 */
	public function max_num_pages( $object_subtype = '' ) {
		$query = $this->get_public_post_authors_query();

		$total_users = $query->get_total();

		return (int) ceil( $total_users / core_sitemaps_get_max_urls( $this->object_type ) );
	}

	/**
	 * Returns a query for authors with public posts.
	 *
	 * Implementation must support `$query->max_num_pages`.
	 *
	 * @since 5.5.0
	 *
	 * @param integer $page_num Optional. Default is 1. Page of query results to return.
	 * @return WP_User_Query
	 */
	public function get_public_post_authors_query( $page_num = 1 ) {
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
				'number'              => core_sitemaps_get_max_urls( $this->object_type ),
				'paged'               => absint( $page_num ),
			)
		);

		return $query;
	}
}
