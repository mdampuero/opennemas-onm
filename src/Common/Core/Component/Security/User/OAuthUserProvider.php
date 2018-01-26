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

use Common\ORM\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseOAuthUserProvider;
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

        // User exists
        if (!empty($user)) {
            // Prevent password deletion after external eraseCredentials call
            return clone($user);
        }

        $user = $this->session->get('user');

        // Create fake user basing on response
        if (empty($user)) {
            return $this->createUser($response);
        }

        try {
            return $this->connectUser($user, $response);
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
     * Connects the current user to the account.
     *
     * @param User     $user     The authenticated user in the current session.
     * @param Response $response The resource response.
     *
     * @return User The authenticated user with the updated information.
     */
    protected function connectUser($user, $response)
    {
        $resource = $response->getResourceOwner()->getName();
        $userId   = $response->getUsername();

        // Connect user in session to the account
        $user = $this->em->getRepository('User', $user->getOrigin())->find($user->id);

        // Connect accounts
        $user->{$resource . '_email'}    = $response->getEmail();
        $user->{$resource . '_id'}       = $userId;
        $user->{$resource . '_realname'} = $response->getRealName();
        $user->{$resource . '_token'}    = $response->getAccessToken();

        $this->em->persist($user);

        // Prevent password deletion after external eraseCredentials call
        return clone($user);
    }

    /**
     * Creates a new fake user basing on the response. This user may or may not
     * be stored in database depending on the request target path.
     *
     * @param Response $response The resource response.
     *
     * @return User The new fake user.
     */
    protected function createUser($response)
    {
        $resource = $response->getResourceOwner()->getName();

        return new User([
            'name'          => $response->getRealName(),
            'username'      => $response->getEmail(),
            'email'         => $response->getEmail(),
            'activated'     => true,
            'type'          => 1,
            'fk_user_group' => [],

            $resource . '_email'    => $response->getEmail(),
            $resource . '_id'       => $response->getUserName(),
            $resource . '_realname' => $response->getRealName(),
            $resource . '_token'    => $response->getAccessToken(),
        ]);
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
                return null;
            }
        }

        return null;
    }
}
