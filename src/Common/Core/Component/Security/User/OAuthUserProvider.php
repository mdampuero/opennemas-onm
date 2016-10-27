<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Component\Security\User;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseOAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * The OAuthProvider class provides methods to load an users basing on the
 * response received from server after logging in a service that supports OAuth.
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
     * The list of repositories to use.
     *
     * @var array
     */
    protected $repositories;

    /**
     * The current session.
     *
     * @var Session
     */
    protected $session;

    /**
     * Creates a new OAuthUserProvider.
     *
     * @param $service_container The service container.
     */
    public function __construct($em, $session, $repositories)
    {
        $this->em           = $em;
        $this->session      = $session;
        $this->repositories = $repositories;
    }

    /**
     * Returns an user basing on the oauth response.
     *
     * @param UserResponseInterface $response The response from the server.
     *
     * @return User The user.
     *
     * @throw UsernameNotFoundException If the user for the received response
     *                                  doesn't exist.
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resource = $response->getResourceOwner()->getName();
        $userId   = $response->getUsername();

        $oql = sprintf('%s_id = "%s"', $resource, $userId);

        foreach ($this->repositories as $name) {
            try {
                return $this->em->getRepository('User', $name)->findOneBy($oql);
            } catch (\Exception $e) {
            }
        }

        $user = $this->session->get('user');

        if (empty($user)) {
            throw new UsernameNotFoundException(
                _('Unable to find an user associated to that account.') . ' '
                . sprintf(
                    _('First you have to link your %s account to your opennemas account.'),
                    $resource
                )
            );
        }

        try {
            $user = $this->em->getRepository('User', $user->getOrigin())
                ->find($user->id);

            // Connect accounts
            $user->{$resource . '_email'}    = $response->getEmail();
            $user->{$resource . '_id'}       = $userId;
            $user->{$resource . '_realname'} = $response->getRealName();
            $user->{$resource . '_token'}    = $response->getAccessToken();

            $this->em->persist($user);

            return $user;
        } catch (\Exception $e) {
        }

        throw new UsernameNotFoundException(_('Unable to link accounts.'));
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
        return $class === 'User';
    }
}
