# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

## [0.3.0]

- Disable sitemaps for private sites discouraging search engines ([#138](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/138))
- Remove `lastmod` field from sitemaps ([#145](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/145))
- Redirect `sitemap.xml` to `wp-sitemap.xml` if possible ([#149](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/149))
- Prevents adding trailing slashes when requesting stylesheets ([#160](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/160))
- Exclude private posts from sitemap even for logged-in users ([#165](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/165))
- Fix rewrites for object subtypes containing underscores ([#168](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/168))
- Various code refactorings ([#150](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/150), [#170](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/170), [#171](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/171), [#172](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/172), [#174](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/174))
- Various documentation improvements ([#173](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/173), [#176](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/176), [#179](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/179))

## [0.2.0]

- Fail gracefully when `SimpleXML` extension is not available ([#142](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/142))
- Fix XSL stylesheets when not using pretty permalinks ([#141](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/141))
- Flush rewrite rules upon plugin activation and deactivation ([#136](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/136))
- Prefix all sitemaps with `wp-` ([#135](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/135))
- Added missing translatable strings ([#117](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/117))
- Ensure correct type conversion for URL counts ([#120](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/120))
- Documentation improvements ([#130](https://github.com/GoogleChromeLabs/wp-sitemaps/pull/130))

## [0.1.0]

- Initial release

[unreleased]: https://github.com/GoogleChromeLabs/wp-sitemaps/compare/v0.3.0...HEAD 
[0.3.0]: https://github.com/GoogleChromeLabs/wp-sitemaps/releases/tag/v0.3.0
[0.2.0]: https://github.com/GoogleChromeLabs/wp-sitemaps/releases/tag/v0.2.0
[0.1.0]: https://github.com/GoogleChromeLabs/wp-sitemaps/releases/tag/v0.1.0
