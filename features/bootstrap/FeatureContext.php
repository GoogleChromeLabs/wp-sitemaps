<?php
/**
 * Provides step definitions for custom DMS requirements.
 */

declare( strict_types = 1 );

namespace HM\Tests\Behat;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ElementNotFoundException;
use PaulGibbs\WordpressBehatExtension\Context\Traits;
use PaulGibbs\WordpressBehatExtension\Util;
use PHPUnit\Framework\Assert as PHPUnit;
use RuntimeException;

/**
 * Provides step definitions for custom DMS requirements.
 */
class FeatureContext extends RawProjectContext {
	use Traits\ContentAwareContextTrait;

	// TODO Basic admin page test.
}
