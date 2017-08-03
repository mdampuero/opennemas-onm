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

use Common\Core\Component\Exception\Instance\InstanceBlockedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
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
        $uri      = $event->getRequest()->getRequestUri();
        $instance = $this->container->get('core.instance');

        // Instance not registered
        if (empty($instance)) {
            return;
        }

        $this->security->setInstance($instance);

        if (!$this->hasSecurity($uri)) {
            return;
        }

        $user      = $this->context->getToken()->getUser();
        $instances = $this->getInstances($user);
        // TODO: Uncomment when checking by category name
        //$categories  = $this->getCategories($user);
        $permissions = $this->getPermissions($user);

        $user = $this->container->get('orm.manager')
            ->getRepository('User', $user->getOrigin())
            ->find($user->id);

        $this->security->setInstances($instances);
        $this->security->setUser($user);
        $this->security->setCategories($user->categories);
        $this->security->setPermissions($permissions);

        if ($this->isAllowed($instance, $user, $uri)) {
            return;
        }

        $this->logout($event, $instance, $uri);
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

        $oql = sprintf(
            'pk_content_category in ["%s"]',
            implode('", "', $user->categories)
        );

        $categories = $this->container->get('orm.manager')
            ->getRepository('Category')
            ->findBy($oql);

        return array_map(function ($a) {
            return $a->name;
        }, $categories);
    }

    /**
     * Returns the list of instances this user owns.
     *
     * @param UserInterface $user The current user.
     *
     * @return array The list of instances.
     */
    protected function getInstances(UserInterface $user)
    {
        $oql = sprintf('owner_id ="%s"', $user->id);

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

        $p           = new \Privilege();
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

    /**
     * Checks if the current request should include security checks.
     *
     * @param string $uri The current URI.
     *
     * @return boolean True if the request should pass security checks. False
     *                 otherwise.
     */
    protected function hasSecurity($uri)
    {
        return !(preg_match('@^/_.*@', $uri)
            || empty($this->context->getToken())
            || empty($this->context->getToken()->getUser())
            || $this->context->getToken()->getUser() === 'anon.');
    }

    /**
     * Checks if the request is allowed basing on the current security status.
     *
     * @param Instance $instance The current instance.
     * @param User     $user     The current user.
     * @param string   $uri      The requested URI.
     *
     * @return boolean True if the request is allowed. False otherwise.
     */
    protected function isAllowed($instance, $user, $uri)
    {
        if (!$user->isEnabled()) {
            return false;
        }

        if (!preg_match('@^/(admin|managerws)/.*@', $uri)) {
            return true;
        }

        return ($this->security->hasPermission('MASTER')
            || ($this->security->hasPermission('PARTNER')
                && $this->security->hasInstance($instance->internal_name))
            || ($user->type === 0 && empty($instance->blocked))
        );
    }

    /**
     * Logs user out basing on current request.
     *
     * @param FilterResponseEvent $event    The response event.
     * @param Instance            $instance The current instance.
     * @param string              $uri      The current URI.
     */
    protected function logout($event, $instance, $uri)
    {
        $exception = new BadCredentialsException();
        $response  = new RedirectResponse($this->router->generate('admin_login'));

        if ($instance->blocked) {
            $exception = new InstanceBlockedException($instance->internal_name);

            // Redirect to last URL
            $target = $event->getRequest()->headers->get('referer');

            // Redirect to login callback when login with social networks
            if (empty($target)) {
                $target = $this->router->generate('admin_login_callback');
            }

            // Prevent redirection to login after logging in
            if (strpos($target, '/admin/login') === false) {
                $event->getRequest()->getSession()
                    ->set('_security.backend.target_path', $target);
            }
        }

        // Logout for web services
        if (preg_match('@^/admin/entityws.*@', $uri)
            || preg_match('@^/manager(ws).*@', $uri)
        ) {
            $response = new JsonResponse($exception->getMessage(), 401);
        }

        // Logout for backend
        if (preg_match('@^/admin.*@', $uri)) {
            $this->context->setToken(null);

            $event->getRequest()->getSession()->set(
                \Symfony\Component\Security\Core\Security::AUTHENTICATION_ERROR,
                $exception
            );
        }

        $event->setResponse($response);
    }
}
