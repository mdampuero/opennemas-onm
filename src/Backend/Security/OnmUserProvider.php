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
     * The cache service.
     *
     * @var AbstractCache
     */
    protected $cache;

    /**
     * The instance manager service.
     *
     * @var InstanceManager
     */
    protected $im;

    /**
     * The user repository service
     *
     * @var UserManager
     */
    protected $um;

    /**
     * Initializes the current user provider.
     *
     * @param CacheInterface  $cache The cache object.
     * @param InstanceManager $im    The instance manager.
     * @param UserRepository  $um    The user repository.
     */
    public function __construct($cache, $im, $um)
    {
        $this->cache = $cache;
        $this->im = $im;
        $this->um = $um;
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
        $criteria = array(
            'union' => 'OR',
            'username' => array(array('value' => $username)),
            'email' => array(array('value' => $username))
        );

        $user = $this->um->findOneBy($criteria);

        if (!$user) {
            $database = $this->um->conn->dbname;
            $this->um->selectDatabase('onm-instances');
            $this->cache->setNamespace('manager');
            $GLOBALS['application']->conn->selectDatabase('onm-instances');

            $user = $this->um->findOneBy($criteria);
            $this->um->conn->selectDatabase($database);
        }

        if (!$user) {
            throw new UsernameNotFoundException(_('Could not find user. Sorry!'));
        }

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
