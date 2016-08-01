<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Initializes the security service and checks if the current request is
 * allowed.
 */
class SecurityListener implements EventSubscriberInterface
{
    /**
     * The service container.
     *
     * @var SecurityContainer
     */
    private $container;

    /**
     * Initializes the listener.
     *
     * @param SecurityContext $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;

        $this->context  = $container->get('security.token_storage');
        $this->router   = $container->get('router');
        $this->security = $container->get('core.security');
    }

    /**
     * Refresh the user roles for an authenticated user.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $uri = $event->getRequest()->getRequestUri();

        if (preg_match('@^/_.*@', $uri)
            || empty($this->context->getToken())
            || empty($this->context->getToken()->getUser())
            || $this->context->getToken()->getUser() === 'anon.'
        ) {
            return;
        }

        $instance    = $this->container->get('core.instance');
        $user        = $this->context->getToken()->getUser();
        $categories  = $this->getCategories($user);
        $permissions = $this->getPermissions($user);

        $this->security->setInstance($instance);
        $this->security->setUser($user);
        $this->security->setCategories($categories);
        $this->security->setPermissions($permissions);

        if ($this->security->hasRole('ROLE_MANAGER') || $user->isEnabled()) {
            return;
        }

        // Logout for web services
        if (preg_match('@^/admin/entityws.*@', $uri)
            || preg_match('@^/manager(ws).*@', $uri)
        ) {
            $event->setResponse(new JsonResponse(null, 401));
            return;
        }

        // Logout for backend
        if (preg_match('@^/admin.*@', $uri)) {
            $event->setResponse(new RedirectResponse(
                $this->router->generate('admin_logout')
            ));
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [ KernelEvents::REQUEST => 'onKernelRequest' ];
    }

    /**
     * Returns the list of categories for the user.
     *
     * @param UserInterface $user The current user.
     *
     * @return array The list of categories.
     */
    protected function getCategories(UserInterface $user)
    {
        if (empty($user->categories)) {
            return [];
        }

        $oql = sprintf('id in [%s]', implode($user->categories));

        $categories = $this->container->get('orm.manager')
            ->getRepository('Category')
            ->findBy($oql);

        return array_map(function ($a) {
            return $a->name;
        }, $categories);
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
            ->getRepository('UserGroup')
            ->findBy($oql);

        $permissions = [];
        foreach ($userGroups as $userGroup) {
            $permissions = array_merge($permissions, $userGroup->privileges);
        }

        $p = new \Privilege();
        $permissions = array_filter(
            $p::$privileges,
            function ($a) use ($permissions) {
                if (in_array($a['pk_privilege'], $permissions)) {
                    return true;
                }

                return false;
            }
        );

        return array_map(function ($a) {
            return $a['name'];
        }, $permissions);
    }
}
