<?php

namespace Backend\Security;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseOAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class OnmOAuthUserProvider extends BaseOAuthUserProvider
{
    protected $cache;
    protected $container;
    protected $session;

    /**
     * Creates a new OAuthUserProvider
     *
     * @param $session
     * @param $service_container
     */
    public function __construct($cache, $session, $service_container)
    {
        $this->cache     = $cache;
        $this->session   = $session;
        $this->container = $service_container;
    }

    /**
     * Returns an user by the given username.
     *
     * @param  string $username User's email.
     * @return User
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
     * @param  UserResponseInterface $response Response from the server.
     * @return User
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

            $this->cache->delete('user_' . $user->id);
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
            throw new UsernameNotFoundException('Invalid user');
        }

        return $user;
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param  string  $class
     * @return boolean
     */
    public function supportsClass($class)
    {
        return $class === 'User';
    }
}
