<?php
/**
 * Base Behat context.
 *
 * Custom contexts should extend from this class. It does not do anything, but
 * allows for easy composition and inheritance of common functionality.
 */

namespace Core_Sitemaps\Tests\Behat;

use PaulGibbs\WordpressBehatExtension\Context\RawWordpressContext;

/**
 * Base Behat context for the DMS.
 *
 * Does not contain any step defintions.
 */
class RawProjectContext extends RawWordpressContext {
}
