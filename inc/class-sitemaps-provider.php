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
	 * Core_Sitemaps_Provider constructor.
	 */
	public function __construct() {
		$this->registry = Core_Sitemaps_Registry::instance();
	}

	/**
	 * Bootstrapping actions and filters.
	 */
	public function bootstrap() {
		add_action( 'save_post_post', array( $this, 'update_page_entry' ), 10, 2 );
		add_action( 'after_delete_post', array( $this, 'delete_page_entry' ) );
	}

	/**
	 * @param int     $post_id Post object ID.
	 * @param WP_Post $post Post object.
	 *
	 * @return bool|int|WP_Error Return wp_insert_post() / wp_update_post() output; or false if no bucket exists.
	 */
	function update_page_entry( $post_id, $post ) {
		return false;
	}

	/**
	 * When a post is deleted, remove page from sitemaps page.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return bool @see wp_update_post()
	 */
	function delete_page_entry( $post_id ) {
		return false;
	}
}
