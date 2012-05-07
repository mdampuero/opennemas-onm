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

use Onm\Framework\Controller\Controller,
    Onm\Message as m;

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
        require_once '../bootstrap.php';
        require_once 'session_bootstrap.php';

        // Setup view
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
        $this->view->assign('version', \Onm\Common\Version::VERSION);
    }

    /**
     * Shows the login form
     *
     * @return string the response string
     **/
    public function defaultAction()
    {
        $token = md5(uniqid(mt_rand(), true));

        $_SESSION['csrf'] = $token;
        $languages = \Application::getAvailableLanguages();
        $currentLanguage = \Application::$language;

        return $this->render('login/login.tpl', array(
            'languages' => $languages,
            'current_language' => $currentLanguage,
            'token' => $token,
        ));
    }

    // TODO: Move session management logic to a specialized class
    /**
     * Gets all the settings and displays the form
     *
     * @return string the response
     **/
    public function processformAction()
    {
        //  Get values from post
        $login    = $this->request->request->filter('login', null, FILTER_SANITIZE_STRING);
        $password = $this->request->request->filter('password', null, FILTER_SANITIZE_STRING);
        $token    = $this->request->request->filter('token', null, FILTER_SANITIZE_STRING);
        $captcha  = '';

        $user = new \User();

        if ($_SESSION['csrf'] !== $token) {
            $this->view->assign('message', _('Login token is not valid. Try to autenticate again.'));
        } else {

            // Try to autenticate the user
            if ($user->login($login, $password, $token, $captcha)) {

                // Check if user account is activated
                if ($user->authorize != 1) {
                    $this->view->assign('message', _('This user was deactivated. Please ask your administrator.'));
                } else {

                    // Increase security by regenerating the id
                    session_regenerate_id();

                    //Delete the cache that handles the number of active sessions
                    apc_delete(APC_PREFIX ."_"."num_sessions");


                    $_SESSION = array(
                        'userid'           => $user->id,
                        'realname'         => $user->firstname . " " . $user->lastname,
                        'username'         => $user->login,
                        'email'            => $user->email,
                        'isAdmin'          => ( \UserGroup::getGroupName($user->fk_user_group)=='Administrador' ),
                        'isMaster'         => ( \UserGroup::getGroupName($user->fk_user_group)=='Masters' ),
                        'privileges'       => \Privilege::get_privileges_by_user($user->id),
                        'accesscategories' => $user->get_access_categories_id(),
                        'authMethod'       => $user->authMethod,
                        'default_expire'   => $user->sessionexpire,
                        'csrf'             => md5(uniqid(mt_rand(), true))
                    );

                    // Store default expire time
                    global $app;
                    $app::setCookieSecure('default_expire', $user->sessionexpire, 0, '/admin/');
                    \PrivilegesCheck::loadSessionExpireTime();
                    $GLOBALS['Session']->cleanExpiredSessionFiles();

                    $forwardTo = filter_input(INPUT_POST, 'forward_to');
                    if (!is_null($forwardTo) && !empty($forwardTo)) {
                        return $this->redirect(SITE_URL.$forwardTo);
                    } else {
                        return $this->redirect(SITE_URL_ADMIN);
                    }
                }

            } else {
                $this->view->assign('message', _('Username or password incorrect.'));
            }
        }
        $token = md5(uniqid(mt_rand(), true));
        $this->view->assign('token', $token);
        $_SESSION['csrf'] = $token;

        return $this->render('login/login.tpl');
    }

    /**
     * Performs the action of saving the configuration settings
     *
     * @return string the response
     **/
    public function logoutAction()
    {
        $csrf = filter_input(INPUT_GET, 'csrf');

        // Only perform session destroy if cross-site request forgery matches the session variable.
        if ($csrf === $_SESSION['csrf']) {
            $_SESSION = array();
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time()-42000, '/');
            }
            // Delete the cache that handles the number of active sessions
            apc_delete(APC_PREFIX . "_"."num_sessions");
            session_destroy();
            $this->redirect(url('admin_login_form'));

        } else {
            echo "Are you hijacking my session dude?!";
        }
    }

} // END class Authentication