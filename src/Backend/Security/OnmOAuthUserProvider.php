<?php

namespace Backend\Security;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseOAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class OnmOAuthUserProvider extends BaseOAuthUserProvider
{

    protected $admins;
    protected $session;
    protected $doctrine;

    /**
     * Creates a new OAuthUserProvider
     *
     * @param $session
     * @param $doctrine
     * @param $service_container
     */
    public function __construct($session, $service_container)
    {
        $this->session = $session;
        // $this->doctrine = $doctrine;
        $this->container = $service_container;
    }

    /**
     * Returns an user by the given username.
     *
     * @param string $username   User's email.
     *
     * @return User
     */
    public function loadUserByUsername($username)
    {
        $user = $this->doctrine->getManager()
            ->getRepository('ModelBundle:User')
            ->findOneByEmail($username);

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
     * @param UserResponseInterface $response   Response from the server.
     *
     * @return User
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        // Data from Google response
        $avatar   = $response->getProfilePicture();
        $email    = $response->getEmail();
        $userId   = $response->getUsername(); /* An ID like: 112259658235204980084 */
        $realname = $response->getRealName();
        $token    = $response->getAccessToken();

        // Check if this user already exists in DB
        $em   = $this->doctrine->getManager();
        $user = $em->getRepository('ModelBundle:User')->findOneByEmail($email);

        if (!$user) {
            // Create user with basic data
            $user = new User();
            $user->setName($realname);
            $user->setType(2);
            $user->setEmail($email);
            $user->setCreatedAt(new \DateTime('now'));
            $em->persist($user);
            $em->flush();

            // Set slug
            $user->setSlug(
                StringUtils::slugify($user->getId() . '-' . $user->getName())
            );

            $em->flush();
        }

        // Update picture only if it hasn't got one.
        if (!$user->getPicture()) {
            $user->setPicture($avatar);
        }

        // Enable user account
        $meta = $user->getMeta('email_checked');
        if (!count($meta)) {
            $meta = new UserMeta();
            $meta->setUser($user);
            $meta->setName('email_checked');
            $meta->setValue(1);
            $em->persist($meta);
        } else {
            $meta = $meta->first();
        }

        $meta->setValue(1);
        $em->flush();

        // Add resource owner user id to database.
        $userIdMeta = $response->getResourceOwner()->getName() . '_id';
        $meta = $user->getMeta($userIdMeta);
        if (!count($meta)) {
            $meta = new UserMeta();
            $meta->setUser($user);
            $meta->setName($userIdMeta);
            $meta->setValue($userId);
            $em->persist($meta);
            $em->flush();
        } else {
            $meta = $meta->first();
            $meta->setValue($userId);
            $em->flush();
        }

        $userTokenMeta = $response->getResourceOwner()->getName() . '_token';
        $meta = $user->getMeta($userTokenMeta);
        if (!count($meta)) {
            $meta = new UserMeta();
            $meta->setUser($user);
            $meta->setName($userTokenMeta);
            $meta->setValue($token);
            $em->persist($meta);
            $em->flush();
        } else {
            $meta = $meta->first();
            $meta->setValue($token);
            $em->flush();
        }

        $user->setUpdatedAt(new \DateTime('now'));
        $em->flush();

        $em->refresh($user);

        return $this->loadUserByUsername($email);
    }

    public function supportsClass($class)
    {
        return $class === 'Common\\ModelBundle\\Entity\\User';
    }
}
