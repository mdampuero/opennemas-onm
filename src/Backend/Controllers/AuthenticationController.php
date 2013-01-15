<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
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
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
        $this->view->assign('version', \Onm\Common\Version::VERSION);
    }

    /**
     * Shows the login form
     *
     * @return string the response string
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
     * @return string the response
     **/
    public function processformAction(Request $request)
    {
        //  Get values from post
        $login    = $this->request->request->filter('login', null, FILTER_SANITIZE_STRING);
        $password = $this->request->request->filter('password', null, FILTER_SANITIZE_STRING);
        $token    = $this->request->request->filter('token', null, FILTER_SANITIZE_STRING);
        $captcha  = '';

        $user = new \User();

        if (array_key_exists('csrf', $_SESSION)
            && $_SESSION['csrf'] !== $token
        ) {
            $this->view->assign(
                'message',
                _('Login token is not valid. Try to autenticate again.')
            );
        } else {
            // Try to autenticate the user
            if ($user->login($login, $password, $token, $captcha)
                && $user->type == 0
            ) {

                // Check if user account is activated
                if ($user->authorize != 1) {
                    $this->view->assign('message', _('This user was deactivated. Please ask your administrator.'));
                } else {
                    // Increase security by regenerating the id
                    $request->getSession()->migrate();

                    $maxSessionLifeTime = (int) s::get('max_session_lifetime', 60);

                    $group = \UserGroup::getGroupName($user->fk_user_group);

                    $_SESSION = array(
                        'userid'           => $user->id,
                        'realname'         => $user->name,
                        'username'         => $user->login,
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
                        'csrf'             => md5(uniqid(mt_rand(), true))
                    );

                    $forwardTo = $request->request->filter('forward_to', null, FILTER_SANITIZE_STRING);

                    return $this->redirect($forwardTo ?: SITE_URL_ADMIN);
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
     * @return string the response
     **/
    public function logoutAction(Request $request)
    {
        $csrf = filter_input(INPUT_GET, 'csrf');

        // Only perform session destroy if csrf token matches the session variable.
        if ($csrf === $_SESSION['csrf']) {
            $_SESSION = array();
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time()-42000, '/');
            }
            // Delete the cache that handles the number of active sessions
            apc_delete(APC_PREFIX . "_"."num_sessions");
            session_destroy();

            return $this->redirect($this->generateUrl('admin_login_form'));

        } else {
            return new Response('Are you hijacking my session dude?!');
        }
    }
}
