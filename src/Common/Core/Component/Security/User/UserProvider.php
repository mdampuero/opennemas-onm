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

use Opennemas\Cache\Core\CacheManager;
use Opennemas\Orm\Core\EntityManager;
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
     * @param EntityManager $em           The entity manager.
     * @param CacheManager  $cache        The cache service.
     * @param array         $repositories The list of repositories to use.
     */
    public function __construct(EntityManager $em, CacheManager $cm, $repositories)
    {
        $this->em           = $em;
        $this->cm           = $cm;
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

                // Force origin for manager users
                $connData = $this->em->getConnection($repository)->getData();
                if (array_key_exists('dbname', $connData)
                    && $connData['dbname'] === 'onm-instances'
                ) {
                    $user->setOrigin('manager');
                    $this->cm->getConnection($repository)
                        ->set('user-' . $user->id, $user);
                }

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
        return $class === 'Common\Model\Entity\User';
    }
}
