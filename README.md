# Core Sitemaps

A feature plugin to integrate basic XML Sitemaps in WordPress Core

## Description

As [originally proposed in June 2019](https://make.wordpress.org/core/2019/06/12/xml-sitemaps-feature-project-proposal/), this feature plugin seeks to integrate basic XML Sitemaps functionality in WordPress Core.

A short explanation of how this plugin works can be found on [this make/core blog post](https://make.wordpress.org/core/2020/01/27/feature-plugin-xml-sitemaps/).

Interested in contributing to this plugin? Feel free to join us in the [#core-sitemaps](https://wordpress.slack.com/archives/CTKTGNJJW) Slack channel.

## Documentation

- Local Setup: [Local Setup Documentation Section](/docs/SETUP.md/).
- Contributing: [Contributing Documentation Section](/docs/CONTRIBUTING.md)
- Testing: [Testing Documentation Section](/docs/TESTING.md).

## Frequently Asked Questions

**How can I fully disable sitemap generation?**

You can use `remove_action( 'init', 'core_sitemaps_get_server' );` to disable initialization of any sitemap functionality.

**How can I disable sitemaps for a certain object type?**

You can use the `core_sitemaps_register_providers` filter to disable sitemap generation for posts, users, or taxonomies.

**How can I disable sitemaps for a certain post type or taxonomy?**

You can use the `core_sitemaps_post_types` filter to disable sitemap generation for posts of a certain type.

By default, only public posts will be represented in the sitemap.

Similarly, the `core_sitemaps_taxonomies` filter can be used to disable sitemap generation for certain taxonomies.

**How can I exclude certain posts / pages / users from the sitemap or add custom ones?**

The `core_sitemaps_taxonomies_url_list`, `core_sitemaps_users_url_list`, and `core_sitemaps_posts_url_list` filters allow you to add or remove URLs as needed.

No UI option is exposed for this.

**How can I change the number of URLs per sitemap?**

Use the `core_sitemaps_max_urls` filter to adjust the maximum number of URLs included in a sitemap. The default value is 2000 URLs.

**How can I change the appearance of the XML sitemaps in the browser using XSL?**

A variety of filters exists to allow you adjust the styling:

* `core_sitemaps_stylesheet_url` - Filter the URL for the sitemap stylesheet.
* `core_sitemaps_stylesheet_index_url` - Filter the URL for the sitemap index stylesheet.
* `core_sitemaps_stylesheet_content` - Filter the content of the sitemap stylesheet.
* `core_sitemaps_index_stylesheet_content` - Filter the content of the sitemap index stylesheet.
* `core_sitemaps_stylesheet_css` - Filter the CSS only for the sitemap stylesheet.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).
