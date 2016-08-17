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
 * Loads an user by username.
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
     * Initializes the current user provider.
     *
     * @param EntityManager $em The entity manager.
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $oql  = sprintf('username = "%s" or email = "%s"', $username, $username);

        try {
            return $this->em->getRepository('User')->findOneBy($oql);
        } catch (\Exception $e) {
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
