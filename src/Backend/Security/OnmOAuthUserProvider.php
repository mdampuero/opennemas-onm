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

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseOAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Loads an user basing on the response received from server after logging in
 * a service that supports OAuth.
 */
class OnmOAuthUserProvider extends BaseOAuthUserProvider
{
    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * Creates a new OAuthUserProvider.
     *
     * @param $service_container The service container.
     */
    public function __construct($service_container)
    {
        $this->container = $service_container;
    }

    /**
     * Returns an user for the given username.
     *
     * @param string $username User's email.
     *
     * @return User The user.
     *
     * @throw UsernameNotFoundException If the user for the given username
     *                                  doesn't exist.
     */
    public function loadUserByUsername($username)
    {
        $user = $this->container->get('user_repository')
            ->findBy(
                array(
                    'username' => array(
                        array('value' => $username)
                    )
                ),
                array('username' => 'asc'),
                1,
                0
            );

        if ($user) {
            return $user;
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
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
        // Data from Google response
        $avatar   = $response->getProfilePicture();
        $email    = $response->getEmail();
        $userId   = $response->getUsername();
        $realname = $response->getRealName();
        $token    = $response->getAccessToken();
        $resource = $response->getResourceOwner()->getName();

        $user = null;
        if ($this->container->get('security.context')->getToken() &&
            $this->container->get('security.context')->getToken()->getUser()
        ) {
            $user = $this->container->get('security.context')->getToken()->getUser();

            // Connect accounts
            $user->setMeta(array($resource . '_email' => $email));
            $user->setMeta(array($resource . '_id' => $userId));
            $user->setMeta(array($resource . '_realname' => $realname));
            $user->setMeta(array($resource . '_token' => $token));
        } else {
            // Log in
            $user = $this->container->get('user_repository')->findByUserMeta(
                array(
                    'meta_key' => array(
                        array('value' => $resource . '_id')
                    ),
                    'meta_value' => array(
                        array('value' => $userId)
                    )
                ),
                array('username' => 'asc'),
                1,
                0
            );

            $user = array_pop($user);
        }

        if (is_null($user) || empty($user)) {
            throw new UsernameNotFoundException(_(
                'Unable to find an associated user to that social account.'
                .' Notice that first you have to associated it from your Opennemas user account.'
            ));
        }

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
        return $class === 'User';
    }
}
