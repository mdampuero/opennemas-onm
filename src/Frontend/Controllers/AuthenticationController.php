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
namespace Frontend\Controllers;

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

        session_name('_onm_sess');
        $this->session = $this->get('session');
        $this->session->start();
    }

    /**
     * Perfoms the login action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function loginAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            $token = md5(uniqid(mt_rand(), true));
            $_SESSION['csrf'] = $token;

            return $this->render(
                'login/login_form.tpl',
                array(
                    'token' => $token
                )
            );
        } else {
            //  Get values from post
            $login    = $request->request->filter('login', null, FILTER_SANITIZE_STRING);
            $password = $request->request->filter('pwd', null, FILTER_SANITIZE_STRING);
            $token    = $request->request->filter('token', null, FILTER_SANITIZE_STRING);
            $captcha  = '';

            $user = new \User();

            $isLoginCorrect = false;
            if ($_SESSION['csrf'] !== $token) {
                $this->view->assign('message', _('Login token is not valid. Try to autenticate again.'));
            } else {

                // Try to autenticate the user, accept credencials from backend
                if ($user->login($login, $password, $token, $captcha)) {

                    // Check if user account is activated
                    if ($user->authorize != 1) {
                        $this->view->assign(
                            'message',
                            _('You have to accept your subscription by e-mail first. Please check your inbox.')
                        );
                    } else {
                        // Increase security by regenerating the id
                        session_regenerate_id();

                        $_SESSION = array(
                            'userid'           => $user->id,
                            'realname'         => $user->name,
                            'username'         => $user->login,
                            'email'            => $user->email,
                            'deposit'          => $user->deposit,
                            'type'             => $user->type,
                            'isAdmin'          => ( \UserGroup::getGroupName($user->fk_user_group)=='Administrador' ),
                            'isMaster'         => ( \UserGroup::getGroupName($user->fk_user_group)=='Masters' ),
                            'privileges'       => \Privilege::get_privileges_by_user($user->id),
                            'accesscategories' => $user->getAccessCategoryIds(),
                            'default_expire'   => $user->sessionexpire,
                            'user_language'    => $user->getMeta('user_language'),
                            'csrf'             => md5(uniqid(mt_rand(), true))
                        );

                        // Store default expire time
                        \Application::setCookieSecure('default_expire', $user->sessionexpire, 0, '/');
                        \PrivilegesCheck::loadSessionExpireTime();

                        $isLoginCorrect = true;
                        $this->view->assign(
                            'success',
                            _('You have logged in correctly. You will be redirect to the Home page')
                        );
                        $this->view->assign('login_ok', $isLoginCorrect);
                    }

                } else {
                    $this->view->assign('message', _('Username or password incorrect.'));
                }
            }

            $token = md5(uniqid(mt_rand(), true));
            $_SESSION['csrf'] = $token;

            return $this->render(
                'login/login_form.tpl',
                array(
                    'token'    => $token,
                    'login_ok' => $isLoginCorrect
                )
            );
        }
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
        $csrf = $request->query->filter('csrf', null, FILTER_SANITIZE_STRING);
        if ($csrf === $_SESSION['csrf']) {
            $_SESSION = array();
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time()-42000, '/');
            }

            session_destroy();

            return new RedirectResponse(SITE_URL);
        } else {
            return new Response("Are you hijacking my session dude?!", 400);
        }
    }

    /**
     * Regenerates the password for a given user
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function regeneratePassword(Request $request)
    {
    }

    /**
     * Sends the user password to the his email address.
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function recoverPass(Request $request)
    {
    }

    /**
     * Shows the profile page of a user
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function show(Request $request)
    {
    }

    /**
     * Updates the user information given POST data
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function update(Request $request)
    {
    }
}
