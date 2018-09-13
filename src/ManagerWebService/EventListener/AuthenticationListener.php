<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\EventListener;

use Common\Core\Component\Security\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * The AuthenticationListener class adds security related information to the
 * authentication resopnse basing on the current user and instance.
 */
class AuthenticationListener implements EventSubscriberInterface
{
    /**
     * The service container.
     *
     * @param ServiceContainer
     */
    protected $container;

    /**
     * Initializes the SecurityListener.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->context   = $container->get('security.token_storage');
    }

    /**
     * Loads an instance basing on the request.
     *
     * @param FilterResponseEvent $event The event object.
     *
     * @return Response|null
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $token    = $this->context->getToken();

        if (empty($token)) {
            return $response;
        }

        $user = $token->getUser();
        $uri  = $event->getRequest()->getRequestUri();

        if (empty($user) || !preg_match('/\/managerws\/check/', $uri)) {
            return $response;
        }

        $data = json_decode($response->getContent(), true);

        $data['instance']    = $this->container->get('core.instance')->getData();
        $data['permissions'] = array_values($this->getPermissions($user));
        $data['instances']   = $this->getInstances($user);

        $response->setContent(json_encode($data));

        return $response;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [ ['onKernelRequest', 100] ],
        ];
    }

    /**
     * Returns the list of instances the user owns.
     *
     * @param UserInterface $user The current user.
     *
     * @return array The list of instances.
     */
    protected function getInstances(UserInterface $user)
    {
        $oql = sprintf('owner_id = "%s"', $user->id);

        $instances = $this->container->get('orm.manager')
            ->getRepository('Instance')
            ->findBy($oql);

        return array_map(function ($a) {
            return $a->internal_name;
        }, $instances);
    }

    /**
     * Returns the list of permissions for the user.
     *
     * @param UserInterface $user The current user.
     *
     * @return array The list of permissions
     */
    protected function getPermissions(UserInterface $user)
    {
        if (empty($user->fk_user_group)) {
            return [];
        }

        $oql = sprintf('pk_user_group in [%s]', implode(',', $user->fk_user_group));

        $userGroups = $this->container->get('orm.manager')
            ->getRepository('UserGroup', $user->getOrigin())
            ->findBy($oql);

        $permissions = [];
        foreach ($userGroups as $userGroup) {
            $permissions = array_merge($permissions, $userGroup->privileges);
        }

        return \Privilege::getNames($permissions);
    }
}
