# Core Sitemaps

A feature plugin to integrate basic XML Sitemaps in WordPress Core.

## Description

As [originally proposed in June 2019](https://make.wordpress.org/core/2019/06/12/xml-sitemaps-feature-project-proposal/), this feature plugin seeks to integrate basic XML Sitemaps functionality into WordPress Core.

A short explanation of how this plugin works can be found on [this make/core blog post](https://make.wordpress.org/core/2020/01/27/feature-plugin-xml-sitemaps/).

Interested in contributing to this plugin? Feel free to join us in the [#core-sitemaps](https://wordpress.slack.com/archives/CTKTGNJJW) Slack channel.

## Documentation

- Local Setup: [Local Setup Documentation Section](/docs/SETUP.md/).
- Contributing: [Contributing Documentation Section](/docs/CONTRIBUTING.md)
- Testing: [Testing Documentation Section](/docs/TESTING.md).

## Frequently Asked Questions

### How can I fully disable sitemap generation?

You can use `remove_action( 'init', 'wp_sitemaps_get_server' );` to disable initialization of any sitemap functionality.

### How can I disable sitemaps for a certain object type?

You can use the `sitemaps_register_providers` filter to disable sitemap generation for posts, users, or taxonomies.

### How can I disable sitemaps for a certain post type or taxonomy?

You can use the `sitemaps_post_types` filter to disable sitemap generation for posts of a certain post type.

By default, only public posts will be represented in the sitemap.

Similarly, the `sitemaps_taxonomies` filter can be used to disable sitemap generation for certain taxonomies.

**Example: Disabling sitemaps for the "page" post type**

```php
add_filter(
	'sitemaps_post_types',
	function( $post_types ) {
		unset( $post_types['page'] );
		return $post_types;
	}
);
```

**Example: Disabling sitemaps for the "post_tag" taxonomy**

```php
add_filter(
	'sitemaps_taxonomies',
	function( $taxonomies ) {
		unset( $taxonomies['post_tag'] );
		return $taxonomies;
	}
);
```

### How can I exclude certain posts / taxonomies / users from the sitemap or add custom ones?

The `sitemaps_taxonomies_url_list`, `sitemaps_taxonomies_url_list`, and `sitemaps_users_url_list` filters allow you to add or remove URLs as needed.

**Example: Ensuring the page with ID 42 is not included**

```php
add_filter(
	'sitemaps_posts_url_list',
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
	'sitemaps_taxonomies_url_list',
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
	'sitemaps_users_url_list',
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

### How can I change the number of URLs per sitemap?

Use the `sitemaps_max_urls` filter to adjust the maximum number of URLs included in a sitemap. The default value is 2000 URLs.

### How can I change the appearance of the XML sitemaps in the browser using XSL?

A variety of filters exists to allow you adjust the styling:

* `sitemaps_stylesheet_url` - Filter the URL for the sitemap stylesheet.
* `sitemaps_stylesheet_index_url` - Filter the URL for the sitemap index stylesheet.
* `sitemaps_stylesheet_content` - Filter the content of the sitemap stylesheet.
* `sitemaps_index_stylesheet_content` - Filter the content of the sitemap index stylesheet.
* `sitemaps_stylesheet_css` - Filter the CSS only for the sitemap stylesheet.

### Does this plugin support `changefreq` and `priority` attributes for sitemaps?

No. Those are optional fields in the sitemaps protocol and not typically consumed by search engines. Developers can still add those fields if they really want to.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).
