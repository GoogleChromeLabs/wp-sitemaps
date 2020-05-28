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

You can use the `wp_sitemaps_register_providers` filter to disable sitemap generation for posts, users, or taxonomies.

### How can I disable sitemaps for a certain post type or taxonomy?

You can use the `wp_sitemaps_post_types` filter to disable sitemap generation for posts of a certain post type.

By default, only public posts will be represented in the sitemap.

Similarly, the `wp_sitemaps_taxonomies` filter can be used to disable sitemap generation for certain taxonomies.

**Example: Disabling sitemaps for the "page" post type**

```php
add_filter(
	'wp_sitemaps_post_types',
	function( $post_types ) {
		unset( $post_types['page'] );
		return $post_types;
	}
);
```

**Example: Disabling sitemaps for the "post_tag" taxonomy**

```php
add_filter(
	'wp_sitemaps_taxonomies',
	function( $taxonomies ) {
		unset( $taxonomies['post_tag'] );
		return $taxonomies;
	}
);
```

### How can I exclude certain posts / taxonomies / users from the sitemap or add custom ones?

The `wp_sitemaps_taxonomies_url_list`, `wp_sitemaps_taxonomies_url_list`, and `wp_sitemaps_users_url_list` filters allow you to add or remove URLs as needed.

**Example: Ensuring the page with ID 42 is not included**

```php
add_filter(
	'wp_sitemaps_posts_url_list',
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
	'wp_sitemaps_taxonomies_url_list',
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
	'wp_sitemaps_users_url_list',
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

### How can I add `changefreq`, `priority`, or `lastmod` to a sitemap?

You can use the `wp_sitemaps_posts_entry` / `wp_sitemaps_users_entry` / `wp_sitemaps_taxonomies_entry` filters to add additional attributes like `changefreq`, `priority`, or `lastmod` to single item in the sitemap.

**Example: Adding the last modified date for posts**

```php
add_filter(
    'wp_sitemaps_posts_entry',
    function( $entry, $post ) {
        $entry['lastmod'] = $post->post_modified_gmt;
        return $entry;
    },
    10,
    2
);
```

Similarly, you can use the `wp_sitemaps_index_entry` filter to add `lastmod` on the sitemap index. Note: `changefreq` and `priority` are not supported on the sitemap index.

### How can I add image sitemaps?

Image sitemaps are not supported by default, but can be added by plugins through a set of filters.

First, use the `wp_sitemaps_urlset_attributes` filter to add the necessary XML namespace:

```php
add_filter(
    'wp_sitemaps_urlset_attributes',
    function( $attributes ) {
        $attributes['xmlns:image'] = 'http://www.google.com/schemas/sitemap-image/1.1';
        return $attributes;
    }
);
```

This makes sure the resulting XML is valid.  After that, use the `wp_sitemaps_posts_entry` filter to add the image data for a post.

```php
add_filter(
    'wp_sitemaps_posts_entry',
    function( $entry, $post ) {
        $entry['image:image'] = array(
            array(
                'image:loc' => 'http://example.com/image.jpg',
                'image:title' => 'Cats',
            ),
            array(
                'image:loc' => 'http://example.com/image2.jpg',
                'image:title' => 'Cats and Dogs',
            ),
        );
        return $entry;
    },
    10,
    2
);
```

### How can I change the number of URLs per sitemap?

Use the `wp_sitemaps_max_urls` filter to adjust the maximum number of URLs included in a sitemap. The default value is 2000 URLs.

### How can I change the appearance of the XML sitemaps in the browser using XSL?

A variety of filters exists to allow you adjust the styling:

* `wp_sitemaps_stylesheet_url` - Filter the URL for the sitemap stylesheet.
* `wp_sitemaps_stylesheet_index_url` - Filter the URL for the sitemap index stylesheet.
* `wp_sitemaps_stylesheet_content` - Filter the content of the sitemap stylesheet.
* `wp_sitemaps_index_stylesheet_content` - Filter the content of the sitemap index stylesheet.
* `wp_sitemaps_stylesheet_css` - Filter the CSS only for the sitemap stylesheet.

### Does this plugin support `changefreq` and `priority` attributes for sitemaps?

No. Those are optional fields in the sitemaps protocol and not typically consumed by search engines. Developers can still add those fields if they really want to.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).
