<?php

namespace Backend\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Onm\Settings as s;
use \Privileges;

class LoginListener
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $securityContext;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $session;

    public function __construct($context, $session)
    {
        $this->securityContext = $context;
        $this->session = $session;
    }


    public function onLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        // Get group(s) of the user
        $group = array();
        $privileges = array();
        $userGroups = $user->id_user_group;

        foreach ($userGroups as $group) {
            $groups[] = \UserGroup::getGroupName($group);
            // Get privileges from user groups
            // $privileges = array_merge(
            //     $privileges,
            //     \Privilege::getPrivilegesForUserGroup($group)
            // );
        }

        // $maxSessionLifeTime = (int) s::get('max_session_lifetime', 60);
        // Set session array
        $_SESSION['userid']                = $user->id;
        $_SESSION['realname']              = $user->name;
        $_SESSION['username']              = $user->username;
        $_SESSION['email']                 = $user->email;
        $_SESSION['deposit']               = $user->deposit;
        $_SESSION['type']                  = $user->type;
        $_SESSION['isAdmin']               = in_array('Administrador', $groups);
        $_SESSION['isMaster']              = in_array('Masters', $groups);
        $_SESSION['privileges']            = $privileges;
        $_SESSION['accesscategories']      = $user->getAccessCategoryIds();
        $_SESSION['updated']               = time();
        // $_SESSION['session_lifetime']      = $maxSessionLifeTime * 60;
        $_SESSION['user_language']         = $user->getMeta('user_language');
        $_SESSION['csrf']                  = md5(uniqid(mt_rand(), true));
        $_SESSION['meta']                  = $user->getMeta();
        $_SESSION['failed_login_attempts'] = 0;
    }
}
