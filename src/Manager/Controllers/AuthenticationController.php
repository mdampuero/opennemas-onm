<?php
/**
 * Handles all the request for Welcome actions
 *
 * @package Manager_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Manager\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles all the request for Welcome actions
 *
 * @package Manager_Controllers
 **/
class AuthenticationController extends Controller
{

    /**
     * Common actions for all the actions
     *
     * @return void
     **/
    public function init()
    {
        // Setup view
        $this->view = new \TemplateManager(TEMPLATE_MANAGER);
        $this->view->assign('version', \Onm\Common\Version::VERSION);
    }

    /**
     * Shows the login form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        $token = md5(uniqid(mt_rand(), true));

        $_SESSION['csrf'] = $token;
        $languages        = $this->container->getParameter('available_languages');
        $currentLanguage  = \Application::$language;

        return $this->render(
            'login/login.tpl',
            array(
                'languages'        => $languages,
                'current_language' => $currentLanguage,
                'token'            => $token,
            )
        );
    }

    // TODO: Move session management logic to a specialized class
    /**
     * Gets all the settings and displays the form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function processformAction(Request $request)
    {
        //  Get values from post
        $login    = $this->request->request->filter('login', null, FILTER_SANITIZE_STRING);
        $password = $this->request->request->filter('password', null, FILTER_SANITIZE_STRING);
        $token    = $this->request->request->filter('token', null, FILTER_SANITIZE_STRING);
        $time     = $this->request->request->filter('time', null, FILTER_SANITIZE_STRING);
        $captcha  = '';

        $user = new \User();

        if ($_SESSION['csrf'] !== $token) {
            $this->view->assign('message', _('Login token is not valid. Try to autenticate again.'));
        } else {

            // Try to autenticate the user
            if ($user->login($login, $password, $token, $captcha, $time)) {

                // Check if user account is activated
                if ($user->authorize != 1) {
                    $this->view->assign(
                        'message',
                        _('This user was deactivated. Please ask your administrator.')
                    );
                } else {

                    // Increase security by regenerating the id
                    session_regenerate_id();

                    $maxSessionLifeTime = (int) s::get('max_session_lifetime', 60);

                    $_SESSION = array(
                        'userid'           => $user->id,
                        'realname'         => $user->name,
                        'username'         => $user->login,
                        'email'            => $user->email,
                        'isMaster'         => ( \UserGroup::getGroupName($user->fk_user_group)=='Masters' ),
                        'default_expire'   => $user->sessionexpire,
                        //?? 'privileges'       => \Privilege::getPrivilegesForUserGroup($user->fk_user_group),
                        //?? 'updated'          => time(),
                        'session_lifetime' => $maxSessionLifeTime * 60,
                        'user_language'    => $user->getMeta('user_language'),
                        'csrf'             => md5(uniqid(mt_rand(), true))
                    );


                    $forwardTo = $request->request->filter('forward_to', null, FILTER_SANITIZE_STRING);

                    return $this->redirect($forwardTo?:$this->generateUrl('manager_welcome'));
                }

            } else {
                $message = _('Username or password incorrect.');
                $this->view->assign('message', $message);
            }
        }
        $token = md5(uniqid(mt_rand(), true));
        $_SESSION['csrf'] = $token;

        return $this->render('login/login.tpl', array('token', $token));
    }

    /**
     * Performs the action of saving the configuration settings
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function logoutAction(Request $request)
    {
        $csrf = filter_input(INPUT_GET, 'csrf');

        // Only perform session destroy if cross-site request
        // forgery matches the session variable.
        //if ($csrf === $_SESSION['csrf']) {
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }
        session_destroy();

        return $this->redirect($this->generateUrl('manager_login_form'));

        // } else {
        //     return new Response('Are you hijacking my session dude?!');
        // }
    }
}
