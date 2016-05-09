<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Security;

use Common\ORM\Core\EntityManager;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Loads an user basing on the username.
 */
class OnmUserProvider implements UserProviderInterface
{
    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Initializes the current user provider.
     *
     * @param EntityManager $em The entity manager.
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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
        $oql = sprintf('username = "%s" or email = "%s"', $username, $username);

        $user = $this->em->getRepository('User', 'instance')->findOneBy($oql);

        if (!$user) {
            $user = $this->em->getRepository('User', 'manager')->findOneBy($oql);
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
