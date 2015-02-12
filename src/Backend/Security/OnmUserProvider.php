<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backend\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Loads an user basing on the username.
 */
class OnmUserProvider implements UserProviderInterface
{
    /**
     * Initializes the OnmUserProvider.
     *
     * @param Onm\Database\DbalWrapper $conn The database connection.
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return AdvancedUserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        if (strpos($username, '\\')) {
            throw new UsernameNotFoundException(_('Could not find user. Sorry!'));
        }

        $user = new \User();
        if (!$user->checkIfExistsUserName($username)
            && !$user->checkIfExistsUserEmail($username)
        ) {
            throw new UsernameNotFoundException(_('Could not find user. Sorry!'));
        }

        $username = $this->conn->quote($username);
        $sql = 'SELECT `id` FROM users WHERE username = ' . $username . ' OR email = ' . $username;
        $results = $this->conn->fetchAssoc($sql);

        $user->read($results['id']);

        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation if it decides to reload the user data
     * from the database, or if it simply merges the passed User into the
     * identity map of an entity manager.
     *
     * @param UserInterface $user The user to refresh.
     *
     * @return UserInterface The refreshed user.
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string  $class The class name.
     *
     * @return boolean True if the given class is supported.
     */
    public function supportsClass($class)
    {
        return $class == 'User';
    }
}
