<?php
/**
 * Sitemaps: Core_Sitemaps_Posts class
 *
 * This class builds the sitemaps for the 'post' object type.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since x.x.x
 */

/**
 * Posts XML sitemap provider.
 */
class Core_Sitemaps_Posts extends Core_Sitemaps_Provider {
	/**
	 * Core_Sitemaps_Posts constructor.
	 */
	public function __construct() {
		$this->object_type = 'post';
		$this->route       = sprintf( '^%s-posts-([A-z]+)-?([0-9]+)?\.xml$', core_sitemaps_sitemap_prefix() );
		$this->slug        = 'posts';
	}

	/**
	 * Return the public post types, which excludes nav_items and similar types.
	 * Attachments are also excluded. This includes custom post types with public = true
	 *
	 * @return array $post_types List of registered object sub types.
	 */
	public function get_object_sub_types() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $post_types['attachment'] );

		/**
		 * Filter the list of post object sub types available within the sitemap.
		 *
		 * @since 0.1.0
		 * @param array $post_types List of registered object sub types.
		 */
		return apply_filters( 'core_sitemaps_post_types', $post_types );
	}

	/**
	 * Query for the Posts add_rewrite_rule.
	 *
	 * @return string Valid add_rewrite_rule query.
	 */
	public function rewrite_query() {
		return 'index.php?sitemap=' . $this->slug . '&sub_type=$matches[1]&paged=$matches[2]';
	}
}
