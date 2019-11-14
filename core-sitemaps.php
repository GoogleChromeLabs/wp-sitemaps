<?php
/**
 * Main setup.
 *
 * @package         Core_Sitemaps
 */

/**
 * Core Sitemaps Plugin.
 *
 * @package         Core_Sitemaps
 * @copyright       2019 The Core Sitemaps Contributors
 * @license         GNU General Public License, version 2
 * @link            https://github.com/GoogleChromeLabs/wp-sitemaps
 *
 * Plugin Name:     Core Sitemaps
 * Plugin URI:      https://github.com/GoogleChromeLabs/wp-sitemaps
 * Description:     A feature plugin to integrate basic XML Sitemaps in WordPress Core
 * Author:          Core Sitemaps Plugin Contributors
 * Author URI:      https://github.com/GoogleChromeLabs/wp-sitemaps/graphs/contributors
 * Text Domain:     core-sitemaps
 * Domain Path:     /languages
 * Version:         0.1.0
 */

const CORE_SITEMAPS_POSTS_PER_PAGE  = 2000;
const CORE_SITEMAPS_MAX_URLS        = 50000;
const CORE_SITEMAPS_REWRITE_VERSION = '20191113c';

require_once __DIR__ . '/inc/class-core-sitemaps.php';
require_once __DIR__ . '/inc/class-core-sitemaps-provider.php';
require_once __DIR__ . '/inc/class-core-sitemaps-index.php';
require_once __DIR__ . '/inc/class-core-sitemaps-posts.php';
require_once __DIR__ . '/inc/class-core-sitemaps-categories.php';
require_once __DIR__ . '/inc/class-core-sitemaps-registry.php';
require_once __DIR__ . '/inc/class-core-sitemaps-renderer.php';
require_once __DIR__ . '/inc/class-core-sitemaps-users.php';
require_once __DIR__ . '/inc/functions.php';

$core_sitemaps = new Core_Sitemaps();
$core_sitemaps->bootstrap();
