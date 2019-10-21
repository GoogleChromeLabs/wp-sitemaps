<?php
/**
 * Provides step definitions for all things relating to users.
 */

namespace Core_Sitemaps\Tests\Behat;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception;
use PaulGibbs\WordpressBehatExtension\Context\Traits;
use RuntimeException;

/**
 * Provides step definitions for all things relating to users.
 *
 * This class was based on:
 * https://github.com/paulgibbs/behat-wordpress-extension/blob/509daae0438ebf5c458cd8cd606e46e3095c19b4/src/Context/UserContext.php
 */
class UserContext extends RawProjectContext {
	use Traits\UserAwareContextTrait, Traits\CacheAwareContextTrait;

	/**
	 * Verify a username is valid and get the matching user information.
	 *
	 * @param string $username Account username.
	 *
	 * @return array The user details matching the given username.
	 * @throws RuntimeException If specified user not found.
	 */
	protected function getUserByName( $username ) {
		$found_user = null;
		$users      = $this->getWordpressParameter( 'users' );

		foreach ( $users as $user ) {
			if ( $username === $user['username'] ) {
				$found_user = $user;
				break;
			}
		}

		if ( $found_user === null ) {
			throw new RuntimeException( "[W801] User not found for name \"{$username}\"" );
		}

		return $found_user;
	}

	/**
	 * Add specified user accounts.
	 *
	 * Example: Given there are users:
	 *     | user_login | user_email        | role          |
	 *     | admin      | admin@example.com | administrator |
	 *
	 * @Given /^(?:there are|there is a) users?:/
	 *
	 * @param TableNode $users Represents data about users to create.
	 */
	public function thereAreUsers( $users ) {
		$params = $this->getWordpressParameters();

		foreach ( $users->getHash() as $user ) {
			if ( ! isset( $user['user_pass'] ) ) {
				$user['user_pass'] = $this->getRandomString();
			}

			$this->createUser( $user['user_login'], $user['user_email'], $user );

			$params['users'][] = array(
				'roles'    => $this->getUserDataFromUsername( 'roles', $user['user_login'] ),
				'username' => $user['user_login'],
				'password' => $user['user_pass'],
			);
		}

		$this->setWordpressParameters( $params );
	}

	/**
	 * Delete the specified user account.
	 *
	 * Example: When I delete the "test" user account
	 *
	 * @When I delete the :user_login user account
	 *
	 * @param string $user_login Account username.
	 */
	public function iDeleteTheUserAccount( $user_login ) {
		$this->deleteUser( $this->getUserIdFromLogin( $user_login ) );
	}

	/**
	 * Go to a user's author archive page.
	 *
	 * Example: When I am viewing posts published by Paul
	 *
	 * @When /^(?:I am|they are) viewing posts published by (.+)$/
	 *
	 * @param string $username Account username.
	 */
	public function iAmViewingAuthorArchive( $username ) {
		$found_user = $this->getUserByName( $username );

		$wordpress_parameters = $this->getWordpressParameters();
		$this->visitPath(
			sprintf(
				$wordpress_parameters['permalinks']['author_archive'],
				$this->getUserDataFromUsername( 'user_nicename', $found_user['username'] )
			)
		);
	}

	/**
	 * Log user out.
	 *
	 * Example: Given I am an anonymous user
	 * Example: When I log out
	 *
	 * @Given /^(?:I am|they are) an anonymous user/
	 * @When I log out
	 *
	 * @throws Exception\DriverException If the browser has not been opened yet (but, it's ok!).
	 */
	public function iAmAnonymousUser() {
			$this->logOut();
	}

	/**
	 * Log user in (with role name).
	 *
	 * Example: Given I am logged in as role contributor
	 *
	 * @Given /^(?:I am|they are) logged in as role (.+)$/
	 *
	 * @param string $role WordPress role name. e.g. "administrator", "editor".
	 *
	 * @throws RuntimeException If specified user not found.
	 * @throws Exception\ExpectationException
	 */
	public function iAmLoggedInAsRole( $role ) {
		$found_user = null;
		$users      = $this->getWordpressParameter( 'users' );

		foreach ( $users as $user ) {
			if ( in_array( $role, $user['roles'], true ) ) {
				$found_user = $user;
				break;
			}
		}

		if ( $found_user === null ) {
			throw new RuntimeException( "[W801] User not found for role \"{$role}\"" );
		}

		$this->logIn( $found_user['username'], $found_user['password'] );
	}

	/**
	 * Log user in (with user name).
	 *
	 * Example: Given I am logged in as user Mince
	 *
	 * @Given /^(?:I am|they are) logged in as user (.+)$/
	 *
	 * @param string $username Account username.
	 */
	public function iAmLoggedInAsUser( $username ) {
		$found_user = $this->getUserByName( $username );

		$this->logIn( $found_user['username'], $found_user['password'] );
	}

	/**
	 * Try to log user in (with role name), but expect failure.
	 *
	 * Example: Then I should not be able to log in as an editor
	 *
	 * @Then /^(?:I|they) should not be able to log in as an? (.+)$/
	 *
	 * @param string $role WordPress role name. e.g. "administrator", "editor".
	 *
	 * @throws Exception\ExpectationException If a user was able to log-in successfully (this should fail).
	 */
	public function iShouldNotBeAbleToLogInAsRole( $role ) {
		try {
			$this->iAmLoggedInAsRole( $role );
		} catch ( RuntimeException $e ) {
			// Expectation fulfilled.
			return;
		}

		throw new Exception\ExpectationException(
			sprintf(
					'[W802] A user with role "%s" was logged-in succesfully. This should not have happened.',
					$role
			),
			$this->getSession()->getDriver()
		);
	}

	/**
	 * Try to log user in (with username), but expect failure.
	 *
	 * Example: Then I should not be able to log in as Scotty
	 *
	 * @Then /^(?:I|they) should not be able to log in as (.+)$/
	 *
	 * @param string $username Account username.
	 *
	 * @throws Exception\ExpectationException If a user was able to log-in successfully (this should fail).
	 */
	public function iShouldNotBeAbleToLogInAsUser( $username ) {
		try {
			$this->iAmLoggedInAsUser( $username );
		} catch ( RuntimeException $e ) {
			// Expectation fulfilled.
			return;
		}

		throw new Exception\ExpectationException(
			sprintf(
				'[W802] The user "%s" was logged-in succesfully. This should not have happened.',
				$username
			),
			$this->getSession()->getDriver()
		);
	}
}
