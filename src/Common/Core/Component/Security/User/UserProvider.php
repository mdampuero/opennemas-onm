<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Security\User;

use Common\ORM\Core\EntityManager;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * The UserProvider class provides methods to load users by username.
 */
class UserProvider implements UserProviderInterface
{
    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * The list of repositories to use.
     *
     * @var array
     */
    protected $repositories;

    /**
     * Initializes the current user provider.
     *
     * @param EntityManager $em The entity manager.
     */
    public function __construct(EntityManager $em, $repositories)
    {
        $this->em           = $em;
        $this->repositories = $repositories;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $oql = sprintf('username = "%s" or email = "%s"', $username, $username);

        foreach ($this->repositories as $repository) {
            try {
                $user = $this->em->getRepository('User', $repository)
                    ->findOneBy($oql);

                // Prevent password deletion when calling eraseCredentials
                return clone($user);
            } catch (\Exception $e) {
            }
        }

        throw new UsernameNotFoundException();
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === 'User';
    }
}
