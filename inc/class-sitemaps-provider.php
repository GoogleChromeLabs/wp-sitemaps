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
		// add_action( 'save_post_post', array( $this, 'update_page_entry' ), 10, 2 );
		// add_action( 'after_delete_post', array( $this, 'delete_page_entry' ) );
	}

	// /**
	//  * @param int     $post_id Post object ID.
	//  * @param WP_Post $post Post object.
	//  *
	//  * @return bool|int|WP_Error Return wp_insert_post() / wp_update_post() output; or false if no bucket exists.
	//  */
	// function update_page_entry( $post_id, $post ) {
	// 	$page_id      = $this->get_page_id( $post_id );
	// 	$query_result = $this->get_page( 'post', $page_id );
	// 	if ( false === $query_result ) {
	// 		return false;
	// 	}
	// 	// if ( count( $query_result ) < 1 ) {
	// 	// 	// Fixme: handle WP_Error.
	// 	// 	return core_sitemaps_bucket_insert( $post, $bucket_num );
	// 	// }
	// 	// /** @noinspection LoopWhichDoesNotLoopInspection */
	// 	// foreach ( $query_result as $page ) {
	// 	// 	// Fixme: handle WP_Error.
	// 	// 	return core_sitemaps_bucket_update( $post, $page );
	// 	// }
	// 	//
	// 	// // Well that's awkward.
	// 	// return false;
	// }
	//
	// /**
	//  * When a post is deleted, remove page from sitemaps page.
	//  *
	//  * @param int $post_id Post ID.
	//  *
	//  * @return bool @see wp_update_post()
	//  */
	// function delete_page_entry( $post_id ) {
	// 	// $bucket_num   = core_sitemaps_page_calculate_bucket_num( $post_id );
	// 	// $query_result = core_sitemaps_bucket_lookup( 'post', $bucket_num );
	// 	// if ( false === $query_result ) {
	// 	// 	return false;
	// 	// }
	// 	// /** @noinspection LoopWhichDoesNotLoopInspection */
	// 	// foreach ( $query_result as $page ) {
	// 	// 	$items = json_decode( $page->post_content, true );
	// 	// 	if ( isset( $items[ $post_id ] ) ) {
	// 	// 		unset( $items[ $post_id ] );
	// 	// 	}
	// 	// 	$page->post_content = wp_json_encode( $items );
	// 	//
	// 	// 	return wp_update_post( $page );
	// 	// }
	// 	//
	// 	// return false;
	// }

	function get_page_id( $post_id ) {
		$hash        = md5( $this->post_type . $post_id );
		$page_length = $this->get_page_length();
		if ( false === $page_length ) {
			return false;
		}

		return substr( $hash, 0, $page_length );

	}

	function get_page_length() {
		return get_option( 'core_sitemaps_' . $this->post_type . '_page_length' );
	}

	function get_page( $post_type, $page_id ) {

	}
}
