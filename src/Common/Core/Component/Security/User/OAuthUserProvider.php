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

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseOAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * The OAuthUserProvider loads an user basing on the response received from
 * server after logging in a service that supports OAuth.
 */
class OAuthUserProvider extends BaseOAuthUserProvider
{
    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * The current session.
     *
     * @var Session
     */
    protected $session;

    /**
     * The list of repositories to use.
     *
     * @var array
     */
    protected $repositories;

    /**
     * Initializes the OAuthUserProvider.
     *
     * @param EntityManager $em           The entity manager.
     * @param Session       $session      The current session.
     * @param array         $repositories The list of repositories to use.
     */
    public function __construct($em, $session, $repositories)
    {
        $this->em           = $em;
        $this->session      = $session;
        $this->repositories = $repositories;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resource = $response->getResourceOwner()->getName();
        $userId   = $response->getUsername();
        $oql      = sprintf('%s_id = "%s"', $resource, $userId);

        $user = $this->loadUserBy($oql);

        if (!empty($user)) {
            // Prevent password deletion after external eraseCredentials call
            return clone($user);
        }

        $user = $this->session->get('user');

        if (empty($user)) {
            throw new UsernameNotFoundException(
                _('Unable to find an user linked to that account.') . ' '
                . sprintf(
                    _('First you have to link your %s account to your opennemas account.'),
                    $resource
                )
            );
        }

        try {
            $user = $this->em->getRepository('User', $user->getOrigin())->find($user->id);

            // Connect accounts
            $user->{$resource . '_email'}    = $response->getEmail();
            $user->{$resource . '_id'}       = $userId;
            $user->{$resource . '_realname'} = $response->getRealName();
            $user->{$resource . '_token'}    = $response->getAccessToken();

            $this->em->persist($user);

            // Prevent password deletion after external eraseCredentials call
            return clone($user);
        } catch (\Exception $e) {
            throw new UsernameNotFoundException(
                sprintf(
                    _('Unable to link your opennemas account to your %s account'),
                    $resource
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === 'User';
    }

    /**
     * Tries to load an user that matches an OQL criteria.
     *
     * @param string $oql The resource name.
     *
     * @return mixed An user from instance or manager if found. Null otherwise.
     */
    protected function loadUserBy($oql)
    {
        foreach ($this->repositories as $name) {
            try {
                return $this->em->getRepository('User', $name)
                    ->findOneBy($oql);
            } catch (\Exception $e) {
            }
        }

        return null;
    }
}
