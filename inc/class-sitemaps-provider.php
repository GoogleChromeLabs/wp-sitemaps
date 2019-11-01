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
		if ( $this->post_type ) {
			$option = 'core_sitemaps_' . $this->post_type . '_page_length';
			add_option( $option, 1, '', false );
		}

	}

	/**
	 * Bootstrapping actions and filters.
	 */
	public function bootstrap() {
		add_action( 'save_post_post', array( $this, 'update_bucket_post' ), 10, 2 );
		add_action( 'after_delete_post', array( $this, 'delete_bucket_post' ) );
	}

	/**
	 * @param int     $post_id Post object ID.
	 * @param WP_Post $post Post object.
	 *
	 * @return bool|int|WP_Error Return wp_insert_post() / wp_update_post() output; or false if no bucket exists.
	 */
	function update_bucket_post( $post_id, $post ) {
		$bucket = $this->get_bucket( $post );

		// return false;
	}

	/**
	 * When a post is deleted, remove page from sitemaps page.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return bool @see wp_update_post()
	 */
	function delete_bucket_post( $post_id ) {
		return false;
	}

	function get_bucket( $post ) {
		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}
		$bucket_id = get_post_meta( $post->ID, 'bucket_id', true );
		if ( false !== $bucket_id ) {
			return get_post( $bucket_id );
		}

		return core_sitemaps_bucket_create( $post );
	}
}
