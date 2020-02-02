<?php
/**
 * Class file for the Core_Sitemaps_Provider class.
 * This class is a base class for other sitemap providers to extend and contains shared functionality.
 *
 * @package Core_Sitemaps
 */

/**
 * Class Core_Sitemaps_Provider
 */
class Core_Sitemaps_Provider {
	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $object_type = '';

	/**
	 * Sub type name.
	 *
	 * @var string
	 */
	protected $sub_type = '';

	/**
	 * Sitemap route
	 *
	 * Regex pattern used when building the route for a sitemap.
	 *
	 * @var string
	 */
	public $route = '';

	/**
	 * Sitemap slug
	 *
	 * Used for building sitemap URLs.
	 *
	 * @var string
	 */
	public $slug = '';

	/**
	 * Set up relevant rewrite rules, actions, and filters.
	 */
	public function setup() {
		// Set up async tasks related to calculating lastmod data.
		add_action( 'core_sitemaps_calculate_lastmod', array( $this, 'calculate_sitemap_lastmod' ), 10, 3 );
		add_action( 'core_sitemaps_update_lastmod_' . $this->slug, array( $this, 'update_lastmod_values' ) );

		if ( ! wp_next_scheduled( 'core_sitemaps_update_lastmod_' . $this->slug ) && ! wp_installing() ) {

			/**
			 * Filter the recurrence value for updating sitemap lastmod values.
			 *
			 * @since 0.1.0
			 *
			 * @param string $recurrence How often the event should subsequently recur. Default 'twicedaily'.
			 *                           See wp_get_schedules() for accepted values.
			 * @param string $type       The object type being handled by this event, e.g. posts, taxonomies, users.
			 */
			$lastmod_recurrence = apply_filters( 'core_sitemaps_lastmod_recurrence', 'twicedaily', $this->slug );

			wp_schedule_event( time(), $lastmod_recurrence, 'core_sitemaps_update_lastmod_' . $this->slug );
		}
	}

	/**
	 * Get a URL list for a post type sitemap.
	 *
	 * @param int    $page_num Page of results.
	 * @param string $type     Optional. Post type name. Default ''.
	 * @return array $url_list List of URLs for a sitemap.
	 */
	public function get_url_list( $page_num, $type = '' ) {
		if ( ! $type ) {
			$type = $this->get_queried_type();
		}

		// Return an empty array if the type is not supported.
		$supported_types = $this->get_object_sub_types();

		if ( ! isset( $supported_types[ $type ] ) ) {
			return array();
		}

		$query = new WP_Query(
			array(
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'post_type'              => $type,
				'posts_per_page'         => core_sitemaps_get_max_urls( $this->slug ),
				'paged'                  => $page_num,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			)
		);

		/**
		 * Returns an array of posts.
		 *
		 * @var array<int, \WP_Post> $posts
		 */
		$posts = $query->get_posts();

		$url_list = array();

		/*
		 * Add a URL for the homepage in the pages sitemap.
		 * Shows only on the first page if the reading settings are set to display latest posts.
		 */
		if ( 'page' === $type && 1 === $page_num && 'posts' === get_option( 'show_on_front' ) ) {
			// Assumes the homepage last modified date is the same as the most recent post.
			$last_modified = get_posts(
				array(
					'numberposts'            => 1,
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
					'update_post_meta_cache' => false,
				)
			);

			// Extract the data needed for home URL to add to the array.
			$url_list[] = array(
				'loc'     => home_url(),
				'lastmod' => mysql2date( DATE_W3C, $last_modified[0]->post_modified_gmt, false ),
			);
		}

		foreach ( $posts as $post ) {
			$url_list[] = array(
				'loc'     => get_permalink( $post ),
				'lastmod' => mysql2date( DATE_W3C, $post->post_modified_gmt, false ),
			);
		}

		/**
		 * Filter the list of URLs for a sitemap before rendering.
		 *
		 * @since 0.1.0
		 *
		 * @param array  $url_list List of URLs for a sitemap.
		 * @param string $type     Name of the post_type.
		 * @param int    $page_num Page of results.
		 */
		return apply_filters( 'core_sitemaps_posts_url_list', $url_list, $type, $page_num );
	}

	/**
	 * Query for the add_rewrite_rule. Must match the number of Capturing Groups in the route regex.
	 *
	 * @return string Valid add_rewrite_rule query.
	 */
	public function rewrite_query() {
		return 'index.php?sitemap=' . $this->slug . '&paged=$matches[1]';
	}

	/**
	 * Return object type being queried.
	 *
	 * @return string Name of the object type.
	 */
	public function get_queried_type() {
		$type = $this->sub_type;

		if ( empty( $type ) ) {
			$type = $this->object_type;
		}

		return $type;
	}

	/**
	 * Query for determining the number of pages.
	 *
	 * @param string $type Optional. Object type. Default is null.
	 * @return int Total number of pages.
	 */
	public function max_num_pages( $type = '' ) {
		if ( empty( $type ) ) {
			$type = $this->get_queried_type();
		}

		$query = new WP_Query(
			array(
				'fields'                 => 'ids',
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'post_type'              => $type,
				'posts_per_page'         => core_sitemaps_get_max_urls( $this->slug ),
				'paged'                  => 1,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			)
		);

		return isset( $query->max_num_pages ) ? $query->max_num_pages : 1;
	}

	/**
	 * Set the object sub_type.
	 *
	 * @param string $sub_type The name of the object subtype.
	 * @return bool Returns true on success.
	 */
	public function set_sub_type( $sub_type ) {
		$this->sub_type = $sub_type;

		return true;
	}

	/**
	 * Get data about each sitemap type.
	 *
	 * @return array List of sitemap types including object subtype name and number of pages.
	 */
	public function get_sitemap_type_data() {
		$sitemap_data = array();

		$sitemap_types = $this->get_object_sub_types();

		foreach ( $sitemap_types as $type ) {
			// Handle lists of post-objects.
			if ( isset( $type->name ) ) {
				$type = $type->name;
			}

			$sitemap_data[] = array(
				'name'   => $type,
				'pages' => $this->max_num_pages( $type ),
			);
		}

		return $sitemap_data;
	}

	/**
	 * List of sitemap pages exposed by this provider.
	 *
	 * The returned data is used to populate the sitemap entries of the index.
	 *
	 * @return array List of sitemaps.
	 */
	public function get_sitemap_entries() {
		$sitemaps = array();

		$sitemap_types = $this->get_sitemap_type_data();

		foreach ( $sitemap_types as $type ) {
			for ( $page = 1; $page <= $type['pages']; $page ++ ) {
				$loc        = $this->get_sitemap_url( $type['name'], $page );
				$lastmod    = $this->get_sitemap_lastmod( $type['name'], $page );
				$sitemaps[] = array(
					'loc'     => $loc,
					'lastmod' => $lastmod,
				);
			}
		}

		return $sitemaps;
	}

	/**
	 * Get the URL of a sitemap entry.
	 *
	 * @param string $name The name of the sitemap.
	 * @param int    $page The page of the sitemap.
	 * @return string The composed URL for a sitemap entry.
	 */
	public function get_sitemap_url( $name, $page ) {
		global $wp_rewrite;

		$basename = sprintf(
			'/sitemap-%1$s.xml',
			// Accounts for cases where name is not included, ex: sitemaps-users-1.xml.
			implode( '-', array_filter( array( $this->slug, $name, (string) $page ) ) )
		);

		$url = home_url( $basename );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$url = add_query_arg(
				array(
					'sitemap'  => $this->slug,
					'sub_type' => $name,
					'paged'    => $page,
				),
				home_url( '/' )
			);
		}

		return $url;
	}

	/**
	 * Get the last modified date for a sitemap page.
	 *
	 * This will be overridden in provider subclasses.
	 *
	 * @param string $name The name of the sitemap.
	 * @param int    $page The page of the sitemap being returned.
	 * @return string The GMT date of the most recently changed date.
	 */
	public function get_sitemap_lastmod( $name, $page ) {
		$type = implode( '_', array_filter( array( $this->slug, $name, (string) $page ) ) );

		// Check for an option.
		$lastmod = get_option( "core_sitemaps_lastmod_$type", '' );

		// If blank, schedule a job.
		if ( empty( $lastmod ) && ! wp_doing_cron() ) {
			$event_args = array( $this->slug, $name, $page );

			// Don't schedule a duplicate job.
			if ( ! wp_next_scheduled( 'core_sitemaps_calculate_lastmod', $event_args ) ) {
				wp_schedule_single_event( time(), 'core_sitemaps_calculate_lastmod', $event_args );
			}
		}

		return $lastmod;
	}

	/**
	 * Calculate lastmod date for a sitemap page.
	 *
	 * Calculated value is saved to the database as an option.
	 *
	 * @param string $type    The object type of the page: posts, taxonomies, users, etc.
	 * @param string $subtype The object subtype if applicable, e.g., post type, taxonomy type.
	 * @param int    $page    The page number.
	 */
	public function calculate_sitemap_lastmod( $type, $subtype, $page ) {
		if ( $type !== $this->slug ) {
			return;
		}

		// Get the list of URLs from this page and sort it by lastmod date.
		$url_list    = $this->get_url_list( $page, $subtype );
		$sorted_list = wp_list_sort( $url_list, 'lastmod', 'DESC' );

		// Use the most recent lastmod value as the lastmod value for the sitemap page.
		$lastmod = reset( $sorted_list )['lastmod'];

		$suffix = implode( '_', array_filter( array( $type, $subtype, (string) $page ) ) );

		update_option( "core_sitemaps_lastmod_$suffix", $lastmod );
	}

	/**
	 * Schedules asynchronous tasks to update lastmod entries for all sitemap pages.
	 */
	public function update_lastmod_values() {
		$sitemap_types = $this->get_sitemap_type_data();

		foreach ( $sitemap_types as $type ) {
			for ( $page = 1; $page <= $type['pages']; $page ++ ) {
				wp_schedule_single_event( time(), 'core_sitemaps_calculate_lastmod', array( $this->slug, $type['name'], $page ) );
			}
		}
	}

	/**
	 * Return the list of supported object sub-types exposed by the provider.
	 *
	 * By default this is the sub_type as specified in the class property.
	 *
	 * @return array List: containing object types or false if there are no subtypes.
	 */
	public function get_object_sub_types() {
		if ( ! empty( $this->sub_type ) ) {
			return array( $this->sub_type );
		}

		/**
		 * To prevent complexity in code calling this function, such as `get_sitemaps()` in this class,
		 * an iterable type is returned. The value false was chosen as it passes empty() checks and
		 * as semantically this provider does not provide sub-types.
		 *
		 * @link https://github.com/GoogleChromeLabs/wp-sitemaps/pull/72#discussion_r347496750
		 */
		return array( false );
	}
}
