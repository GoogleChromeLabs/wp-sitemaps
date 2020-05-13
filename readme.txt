=== Core Sitemaps ===
Contributors: joemcgill, pacifika, kburgoine, tweetythierry, swissspidy
Tags: seo, sitemaps
Requires at least: 5.4
Tested up to: 5.5
Requires PHP: 5.6
Stable tag: 0.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A feature plugin to integrate basic XML Sitemaps in WordPress Core.

== Description ==

As [originally proposed in June 2019](https://make.wordpress.org/core/2019/06/12/xml-sitemaps-feature-project-proposal/), this feature plugin seeks to integrate basic XML Sitemaps functionality in WordPress Core.

A short explanation of how this plugin works can be found on [this make/core blog post](https://make.wordpress.org/core/2020/01/27/feature-plugin-xml-sitemaps/).

Interested in contributing to this plugin? Feel free to [join us on GitHub](https://github.com/GoogleChromeLabs/wp-sitemaps) and the [#core-sitemaps](https://wordpress.slack.com/archives/CTKTGNJJW) Slack channel.

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

You can use `remove_action( 'init', 'core_sitemaps_get_server' );` to disable initialization of any sitemap functionality.

= How can I disable sitemaps for a certain object type? =

You can use the `core_sitemaps_register_providers` filter to disable sitemap generation for posts, users, or taxonomies.

= How can I disable sitemaps for a certain post type or taxonomy? =

You can use the `core_sitemaps_post_types` filter to disable sitemap generation for posts of a certain post type.

By default, only public posts will be represented in the sitemap.

Similarly, the `core_sitemaps_taxonomies` filter can be used to disable sitemap generation for certain taxonomies.

**Example: Disabling sitemaps for the "page" post type**

```php
add_filter(
	'core_sitemaps_post_types',
	function( $post_types ) {
		unset( $post_types['page'] );
		return $post_types;
	}
);
```

**Example: Disabling sitemaps for the "post_tag" taxonomy**

```php
add_filter(
	'core_sitemaps_taxonomies',
	function( $taxonomies ) {
		unset( $taxonomies['post_tag'] );
		return $taxonomies;
	}
);
```

= How can I exclude certain posts / taxonomies / users from the sitemap or add custom ones? =

The `core_sitemaps_taxonomies_url_list`, `core_sitemaps_taxonomies_url_list`, and `core_sitemaps_users_url_list` filters allow you to add or remove URLs as needed.

**Example: Ensuring the page with ID 42 is not included**

```php
add_filter(
	'core_sitemaps_posts_url_list',
	function( $urls, $type ) {
		if ( 'page' === $type ) {
			$post_to_remove = array( 'loc' => get_permalink( 42 ) );
			$key = array_search( $post_to_remove, $urls, true );
			if ( false !== $key ) {
				array_splice( $urls, $key, 1 );
			}
		}
		return $urls;
	},
	10,
	2
);
```

**Example: Ensuring the category with ID 1 is not included**

```php
add_filter(
	'core_sitemaps_taxonomies_url_list',
	function( $urls, $type ) {
		if ( 'category' === $type ) {
			$term_to_remove = array( 'loc' => get_term_link( 1 ) );
			$key = array_search( $term_to_remove, $urls, true );
			if ( false !== $key ) {
				array_splice( $urls, $key, 1 );
			}
		}
		return $urls;
	},
	10,
	2
);
```

**Example: Ensuring the user with ID 1 is not included**

```php
add_filter(
	'core_sitemaps_users_url_list',
	function( $urls ) {
		$user_to_remove = array( 'loc' => get_author_posts_url( 1 ) );
		$key = array_search( $user_to_remove, $urls, true );
		if ( false !== $key ) {
			array_splice( $urls, $key, 1 );
		}
		return $urls;
	}
);
```

= How can I change the number of URLs per sitemap? =

Use the `core_sitemaps_max_urls` filter to adjust the maximum number of URLs included in a sitemap. The default value is 2000 URLs.

= How can I change the appearance of the XML sitemaps in the browser using XSL? =

A variety of filters exist to allow you to adjust the styling:

* `core_sitemaps_stylesheet_url` - Filter the URL for the sitemap stylesheet.
* `core_sitemaps_stylesheet_index_url` - Filter the URL for the sitemap index stylesheet.
* `core_sitemaps_stylesheet_content` - Filter the content of the sitemap stylesheet.
* `core_sitemaps_index_stylesheet_content` - Filter the content of the sitemap index stylesheet.
* `core_sitemaps_stylesheet_css` - Filter the CSS only for the sitemap stylesheet.

= Does this plugin support `changefreq` and `priority` attributes for sitemaps? =

No. Those are optional fields in the sitemaps protocol and not typically consumed by search engines. Developers can still add those fields if they really want to.

= Why is there no last modified date shown in the sitemap? =

XML sitemaps are first and foremost a discovery mechanism for content. Exposing the date the content was last modified is not needed for the majority of sites.

== Changelog ==

For the plugin's changelog, please check out the full list of changes [on GitHub](https://github.com/GoogleChromeLabs/wp-sitemaps/blob/master/CHANGELOG.md).
