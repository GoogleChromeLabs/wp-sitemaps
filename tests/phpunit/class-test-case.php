<?php
/**
 * A factory for making WordPress data with a cross-object type API.
 *
 * @package Core_Sitemaps
 * @copyright 2019 The Core Sitemaps Contributors
 * @license   GNU General Public License, version 2
 * @link      https://github.com/GoogleChromeLabs/wp-sitemaps
 */

namespace Core_Sitemaps\Tests\Phpunit;

use WP_UnitTestCase;

/**
 * Defines a basic fixture to run multiple tests.
 *
 * All unit tests for the DMS project should inherit from this class.
 */
class Test_Case extends WP_UnitTestCase {

	/**
	 * Runs a routine before setting up all tests.
	 *
	 * Cribbed from BuddyPress so that `self::factory()` calls comes from this class.
	 */
	public static function setUpBeforeClass() {
		$c = self::get_called_class();
		if ( ! method_exists( $c, 'wpSetUpBeforeClass' ) ) {
			self::commit_transaction();
			return;
		}

		call_user_func( array( $c, 'wpSetUpBeforeClass' ), self::factory() );

		self::commit_transaction();
	}

	/**
	 * Fetches the factory object for generating WordPress fixtures.
	 *
	 * @return Factory The fixture factory.
	 */
	protected static function factory() {
		static $factory = null;

		if ( ! $factory ) {
			$factory = new Factory();
		}

		return $factory;
	}

}
