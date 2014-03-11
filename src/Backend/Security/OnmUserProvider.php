<?php

namespace Backend\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class OnmUserProvider implements UserProviderInterface
{
    protected $user;

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @throws UsernameNotFoundException if the user is not found
     * @param  string $username The username
     *
     * @return AdvancedUserInterface
     */
    public function loadUserByUsername($username)
    {
        $user = new \User();

        if ($user->checkIfExistsUserName($username)
            || $user->checkIfExistsUserEmail($username)
        ) {
            $sql = 'SELECT `id` FROM users WHERE username = ? OR email = ?';
            $rs = $GLOBALS['application']->conn->Execute($sql, array($username, $username));

            $user->read($rs->fields['id']);

            return $user;
        } else {
            throw new UsernameNotFoundException('Could not find user. Sorry!');
        }
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation if it decides to reload the user data
     * from the database, or if it simply merges the passed User into the
     * identity map of an entity manager.
     *
     * @throws UnsupportedUserException if the account is not supported
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     *
     * @return boolean
     */
    public function supportsClass($class)
    {
        return $class == 'User';
    }
}
