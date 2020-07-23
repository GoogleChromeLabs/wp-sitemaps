=== Core Sitemaps ===
Contributors: joemcgill, pacifika, kburgoine, tweetythierry, swissspidy, pbiron
Tags: seo, sitemaps
Requires at least: 5.4
Tested up to: 5.5
Requires PHP: 5.6
Stable tag: 0.4.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A feature plugin to integrate basic XML Sitemaps in WordPress Core.

== Description ==

**Note: This feature has been integrated into WordPress 5.5. If you run WordPress 5.5, you can freely disable this plugin.**

As [originally proposed in June 2019](https://make.wordpress.org/core/2019/06/12/xml-sitemaps-feature-project-proposal/), this feature plugin seeks to integrate basic XML Sitemaps functionality in WordPress Core.

A short explanation of how this plugin works can be found on [this make/core blog post](https://make.wordpress.org/core/2020/01/27/feature-plugin-xml-sitemaps/).

Interested in contributing to this plugin? Feel free to [join us on GitHub](https://github.com/GoogleChromeLabs/wp-sitemaps) and the [#core-sitemaps](https://wordpress.slack.com/archives/CTKTGNJJW) Slack channel.

=== Available Hooks and Filters ===

**General:**

* `wp_sitemaps_enabled` - Filters whether XML Sitemaps are enabled or not.
* `wp_sitemaps_max_urls` - Filters the maximum number of URLs displayed on a sitemap.
* `wp_sitemaps_register_providers` - Filters the list of registered sitemap providers.
* `wp_sitemaps_init` - Fires when initializing sitemaps.
* `wp_sitemaps_index_entry` - Filters the sitemap entry for the sitemap index.

**Providers:**

* `wp_sitemaps_post_types` - Filters the list of post types to include in the sitemaps.
* `wp_sitemaps_posts_entry` - Filters the sitemap entry for an individual post.
* `wp_sitemaps_posts_show_on_front_entry` - Filters the sitemap entry for the home page when the 'show_on_front' option equals 'posts'.
* `wp_sitemaps_posts_query_args` - Filters the query arguments for post type sitemap queries.
* `wp_sitemaps_posts_pre_url_list` - Filters the posts URL list before it is generated (short-circuit).
* `wp_sitemaps_posts_pre_max_num_pages` - Filters the max number of pages before it is generated (short-circuit).
* `wp_sitemaps_taxonomies` - Filters the list of taxonomies to include in the sitemaps.
* `wp_sitemaps_taxonomies_entry` - Filters the sitemap entry for an individual term.
* `wp_sitemaps_taxonomies_query_args` - Filters the query arguments for taxonomy terms sitemap queries.
* `wp_sitemaps_taxonomies_pre_url_list` - Filters the taxonomies URL list before it is generated (short-circuit).
* `wp_sitemaps_taxonomies_pre_max_num_pages` - Filters the max number of pages before it is generated (short-circuit).
* `wp_sitemaps_users_entry` - Filters the sitemap entry for an individual user.
* `wp_sitemaps_users_query_args` - Filters the query arguments for user sitemap queries.
* `wp_sitemaps_users_pre_url_list` - Filters the users URL list before it is generated (short-circuit).
* `wp_sitemaps_users_pre_max_num_pages` - Filters the max number of pages before it is generated (short-circuit).

**Stylesheets:**

* `wp_sitemaps_stylesheet_css` - Filters the CSS for the sitemap stylesheet.
* `wp_sitemaps_stylesheet_url` - Filters the URL for the sitemap stylesheet.
* `wp_sitemaps_stylesheet_content` - Filters the content of the sitemap stylesheet.
* `wp_sitemaps_stylesheet_index_url` - Filters the URL for the sitemap index stylesheet.
* `wp_sitemaps_stylesheet_index_content` - Filters the content of the sitemap index stylesheet.

== Installation ==

= Installation from within WordPress =

1. Visit **Plugins > Add New**.
2. Search for **Core Sitemaps**.
3. Install and activate the Core Sitemaps plugin.

= Manual installation =

1. Upload the entire `core-sitemaps` folder to the `/wp-content/plugins/` directory.
2. Visit **Plugins**.
3. Activate the Core Sitemaps plugin.

== Frequently Asked Questions ==

= How can I fully disable sitemap generation? =

If you update the WordPress settings to discourage search engines from indexing your site, sitemaps will be disabled.
Alternatively, use the `wp_sitemaps_enabled` filter, or use `remove_action( 'init', 'wp_sitemaps_get_server' );` to disable initialization of any sitemap functionality.

= How can I disable sitemaps for a certain object type? =

You can use the `wp_sitemaps_register_providers` filter to disable sitemap generation for posts, users, or taxonomies.

= How can I disable sitemaps for a certain post type or taxonomy? =

You can use the `wp_sitemaps_post_types` filter to disable sitemap generation for posts of a certain post type.

By default, only public posts will be represented in the sitemap.

Similarly, the `wp_sitemaps_taxonomies` filter can be used to disable sitemap generation for certain taxonomies.

**Example: Disabling sitemaps for the "page" post type**

`
add_filter(
	'wp_sitemaps_post_types',
	function( $post_types ) {
		unset( $post_types['page'] );
		return $post_types;
	}
);
`

**Example: Disabling sitemaps for the "post_tag" taxonomy**

`
add_filter(
	'wp_sitemaps_taxonomies',
	function( $taxonomies ) {
		unset( $taxonomies['post_tag'] );
		return $taxonomies;
	}
);
`

= How can I exclude certain posts / taxonomies / users from the sitemap or add custom ones? =

The `wp_sitemaps_posts_query_args`, `wp_sitemaps_taxonomies_query_args`, and `wp_sitemaps_users_query_args` filters can be used to modify the underlying queries. Using these queries, certain items can be excluded.

**Example: Ensuring the page with ID 42 is not included**

`
add_filter(
	'wp_sitemaps_posts_query_args',
	function( $args ) {
		$args['post__not_in'] = isset( $args['post__not_in'] ) ? $args['post__not_in'] : array();
		$args['post__not_in'][] = 42;
		return $args;
	}
);
`

**Example: Ensuring the category with ID 7 is not included**

`
add_filter(
	'wp_sitemaps_taxonomies_query_args',
	function( $args ) {
		$args['exclude'] = isset( $args['exclude'] ) ? $args['exclude'] : array();
		$args['exclude'][] = 7;
		return $args;
	}
);
`

**Example: Ensuring the user with ID 1 is not included**

`
add_filter(
	'wp_sitemaps_users_query_args',
	function( $args ) {
		$args['exclude'] = isset( $args['exclude'] ) ? $args['exclude'] : array();
		$args['exclude'][] = 1;
		return $args;
	}
);
`

= How can I add `changefreq`, `priority`, or `lastmod` to a sitemap? =

You can use the `wp_sitemaps_posts_entry` / `wp_sitemaps_users_entry` / `wp_sitemaps_taxonomies_entry` filters to add additional attributes like `changefreq`, `priority`, or `lastmod` to single item in the sitemap.

**Example: Adding the last modified date for posts**

`
add_filter(
    'wp_sitemaps_posts_entry',
    function( $entry, $post ) {
        $entry['lastmod'] = $post->post_modified_gmt;
        return $entry;
    },
    10,
    2
);
`

Similarly, you can use the `wp_sitemaps_index_entry` filter to add `lastmod` on the sitemap index. Note: `changefreq` and `priority` are not supported on the sitemap index.

= How can I add image sitemaps? =

Adding image sitemaps are not supported yet, but support will be added in the future so that plugin developers can add them if needed.

= How can I change the number of URLs per sitemap? =

Use the `wp_sitemaps_max_urls` filter to adjust the maximum number of URLs included in a sitemap. The default value is 2000 URLs.

= How can I change the appearance of the XML sitemaps in the browser using XSL? =

A variety of filters exist to allow you to adjust the styling:

* `wp_sitemaps_stylesheet_url` - Filter the URL for the sitemap stylesheet.
* `wp_sitemaps_stylesheet_index_url` - Filter the URL for the sitemap index stylesheet.
* `wp_sitemaps_stylesheet_content` - Filter the content of the sitemap stylesheet.
* `wp_sitemaps_index_stylesheet_content` - Filter the content of the sitemap index stylesheet.
* `wp_sitemaps_stylesheet_css` - Filter the CSS only for the sitemap stylesheet.

= Does this plugin support `changefreq` and `priority` attributes for sitemaps? =

No. Those are optional fields in the sitemaps protocol and not typically consumed by search engines. Developers can still add those fields if they really want to.

= Why is there no last modified date shown in the sitemap? =

XML sitemaps are first and foremost a discovery mechanism for content. Exposing the date the content was last modified is not needed for the majority of sites.

== Changelog ==

For the plugin's changelog, please check out the full list of changes [on GitHub](https://github.com/GoogleChromeLabs/wp-sitemaps/blob/master/CHANGELOG.md).
