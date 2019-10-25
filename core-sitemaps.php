<?php
/**
 * @package Core_Sitemaps
 * @copyright 2019 The Core Sitemaps Contributors
 * @license   GNU General Public License, version 2
 * @link      https://github.com/GoogleChromeLabs/wp-sitemaps
 *
 * Plugin Name:     Core Sitemaps
 * Plugin URI:      https://github.com/GoogleChromeLabs/wp-sitemaps
 * Description:     A feature plugin to integrate basic XML Sitemaps in WordPress Core
 * Author:          Core Sitemaps Plugin Contributors
 * Author URI:      https://github.com/GoogleChromeLabs/wp-sitemaps/graphs/contributors
 * Text Domain:     core-sitemaps
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Core_Sitemaps
 */

// Your code starts here.

require_once __DIR__ . '/inc/sitemaps-index.php';

new Core_Sitemaps_Index();
