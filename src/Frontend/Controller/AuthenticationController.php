<?php
/**
 * Handles the actions for the user authentication in frontend
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the user authentication in frontend
 *
 * @package Frontend_Controllers
 **/
class AuthenticationController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
    }

    /**
     * Performs the login action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function loginAction(Request $request)
    {
        $session = $this->container->get('session');
        $session->start();
        $this->container->get('request')->setSession($session);
        $contentId = $request->query->filter('content_id', '', FILTER_SANITIZE_STRING);

        if ('POST' == $request->getMethod()) {
            //  Get values from post
            $login     = $request->request->filter('login', null, FILTER_SANITIZE_STRING);
            $password  = $request->request->filter('password', null, FILTER_SANITIZE_STRING);
            $token     = $request->request->filter('token', null, FILTER_SANITIZE_STRING);
            $captcha   = '';

            $user = new \User();

            if (array_key_exists('csrf', $_SESSION)
                && $_SESSION['csrf'] !== $token
            ) {
                m::add(_('Login token is not valid. Try to autenticate again.'), m::ERROR);
                return $this->redirect($this->generateUrl('frontend_auth_login'));
            } else {
                // Try to autenticate the user
                if ($user->login($login, $password, $token, $captcha)) {
                    // Check if user account is activated
                    if ($user->activated != 1) {
                        m::add(_('This user is not activated. Check your e-mail for the activation link.'), m::ERROR);
                        return $this->redirect($this->generateUrl('frontend_auth_login'));
                    } else {
                        // Increase security by regenerating the id
                        $request->getSession()->migrate();

                        $maxSessionLifeTime = (int) s::get('max_session_lifetime', 60);

                        // Set last login date
                        $user->setLastLoginDate();

                        $group = \UserGroup::getGroupName($user->fk_user_group);

                        $_SESSION = array(
                            'userid'           => $user->id,
                            'realname'         => $user->name,
                            'username'         => $user->username,
                            'email'            => $user->email,
                            'deposit'          => $user->deposit,
                            'type'             => $user->type,
                            'isAdmin'          => ($group == 'Administrador'),
                            'isMaster'         => ($group == 'Masters'),
                            'privileges'       => \Privilege::getPrivilegesForUserGroup($user->fk_user_group),
                            'accesscategories' => $user->getAccessCategoryIds(),
                            'updated'          => time(),
                            'session_lifetime' => $maxSessionLifeTime * 60,
                            'user_language'    => $user->getMeta('user_language'),
                            'csrf'             => md5(uniqid(mt_rand(), true)),
                            'meta'             => $user->getMeta(),
                        );

                        m::add(_('Log in succesful.'), m::SUCCESS);

                        if (!empty($contentId)) {
                            $article = new \Article($contentId);
                            return $this->redirect($article->uri);
                        }

                        return $this->redirect($this->generateUrl('frontend_user_show'));
                    }

                } else {
                    m::add(_('Username or password incorrect.'), m::ERROR);
                    return $this->redirect($this->generateUrl('frontend_auth_login'));
                }
            }
        }

        // If the session was already initialized redirect the user to user page
        if (array_key_exists('userid', $_SESSION)) {
            return $this->redirect($this->generateUrl('frontend_user_show'));
        }

        $token = md5(uniqid(mt_rand(), true));
        $_SESSION['csrf'] = $token;

        return $this->render(
            'authentication/login.tpl',
            array(
                'token' => $token,
                'id'    => $contentId
            )
        );
    }

    /**
     * Performs the log out action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function logoutAction(Request $request)
    {
        // $csrf = $request->query->filter('csrf', null, FILTER_SANITIZE_STRING);
        // if ($csrf === $_SESSION['csrf']) {
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }

        session_destroy();

        return new RedirectResponse(SITE_URL);
    }
}
