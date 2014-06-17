<?php

namespace Backend\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

use Onm\Settings as s;
use \Privileges;

class OAuthLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var Symfony\Component\Security\Core\SecurityContext
     */
    private $context;

    /**
     * @var Symfony\Component\Routing\Router
     */
    protected $router;

    /**
     * @var Symfony\Component\Security\Core\SecurityContext
     */
    private $session;

    /**
     * Constructs a new handler.
     *
     * @param Symfony\Component\Security\Core\SecurityContext $context
     * @param Symfony\Component\Routing\Router                $router
     * @param Symfony\Component\HttpFoundation\Session        $session
     */
    public function __construct($context, $router, $session)
    {
        $this->context = $context;
        $this->router  = $router;
        $this->session = $session;
    }

    /**
     * This is called when an interactive authentication attempt succeeds.
     *
     * @param  Request        $request
     * @param  TokenInterface $token
     * @return Response                The response to return.
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token
    ) {
        $group      = array();
        $referer    = $this->router->generate('admin_welcome');
        $user       = $token->getUser();
        $userGroups = $user->id_user_group;
        $valid      = true;

        foreach ($userGroups as $group) {
            $groups[] = \UserGroup::getGroupName($group);
        }

        // Set session array
        $_SESSION['userid']           = $user->id;
        $_SESSION['realname']         = $user->name;
        $_SESSION['username']         = $user->username;
        $_SESSION['email']            = $user->email;
        $_SESSION['deposit']          = $user->deposit;
        $_SESSION['type']             = $user->type;
        $_SESSION['accesscategories'] = $user->getAccessCategoryIds();
        $_SESSION['updated']          = time();
        $_SESSION['user_language']    = $user->getMeta('user_language');
        $_SESSION['valid']            = $valid;
        $_SESSION['meta']             = $user->getMeta();


        if ($this->session->get('_security.backend.target_path')) {
            $referer = $this->session->get('_security.backend.target_path');
        }

        return new RedirectResponse($referer);
    }
}
