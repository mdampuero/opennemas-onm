<?php
/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
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
use Onm\Message as m;

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
        $this->view->assign('version', \Onm\Common\Version::VERSION);
        $this->view->assign('languages', $this->container->getParameter('available_languages'));

        // Load reCaptcha lib
        require_once 'recaptchalib.php';
    }

    /**
     * Shows the login form
     *
     * @param Request $request the request object
     *
     * @return string the response string
     **/
    public function defaultAction(Request $request)
    {
        $token = md5(uniqid(mt_rand(), true));

        $_SESSION['csrf'] = $token;
        $currentLanguage  = \Application::$language;

        return $this->render(
            'login/login.tpl',
            array(
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
     * @return string the response
     **/
    public function processformAction(Request $request)
    {
        //  Get values from post
        $login    = $this->request->request->filter('login', null, FILTER_SANITIZE_STRING);
        $password = $this->request->request->filter('password', null, FILTER_SANITIZE_STRING);
        $token    = $this->request->request->filter('token', null, FILTER_SANITIZE_STRING);
        $time     = $this->request->request->filter('time', null, FILTER_SANITIZE_STRING);
        $captcha  = '';

        // reCaptcha vars if setted
        $rcChallengeField = $this->request->request->filter('recaptcha_challenge_field', '');
        $rcResponseField  = $this->request->request->filter('recaptcha_response_field', '');

        // Configs for bad login
        $badLoginLimit = 3; // 3 failed attempts
        $lockoutTime = 600; // 10 minutes

        $user = new \User();

        if (array_key_exists('csrf', $_SESSION)
            && $_SESSION['csrf'] !== $token
        ) {
            m::add(_('Login token is not valid. Try to autenticate again.'), m::ERROR);
            return $this->redirect($this->generateUrl('admin_login_form'));
        } else {
            if (s::get('failed_login_attempts') >= $badLoginLimit) {
                // Get reCaptcha validate response
                $resp = recaptcha_check_answer(
                    '6LfLDtMSAAAAAGTj40fUQCrjeA1XkoVR2gbG9iQs',
                    $this->request->getClientIp(),
                    $rcChallengeField,
                    $rcResponseField
                );

                // What happens when the CAPTCHA was entered incorrectly
                if (!$resp->is_valid) {
                    m::add(_("The reCAPTCHA wasn't entered correctly. Try to autenticate again."), m::ERROR);
                    return $this->redirect($this->generateUrl('admin_login_form'));
                }
            }

            // Try to autenticate the user
            if ($user->login($login, $password, $token, $captcha, $time)
                && $user->type == 0
            ) {

                // Check if user account is activated
                if ($user->activated != 1) {
                    m::add(_('This user was deactivated. Please ask your administrator.'), m::ERROR);
                    return $this->redirect($this->generateUrl('admin_login_form'));
                } elseif ($user->type != 0) {
                    m::add(_('This user has no access to the control panel.'), m::ERROR);
                    return $this->redirect($this->generateUrl('admin_login_form'));
                } else {
                    // Increase security by regenerating the id
                    $request->getSession()->migrate();

                    $maxSessionLifeTime = (int) s::get('max_session_lifetime', 60);

                    // Set bad login counter to 0
                    s::set('failed_login_attempts', 0);

                    // Get group(s) of the user
                    $group = array();
                    $privileges = array();
                    $userGroups = $user->fk_user_group;
                    foreach ($userGroups as $group) {
                        $groups[] = \UserGroup::getGroupName($group);
                        // Get privileges from user groups
                        $privileges = array_merge(
                            $privileges,
                            \Privilege::getPrivilegesForUserGroup($group)
                        );
                    }

                    // Set last login time
                    $currentTime = new \DateTime();
                    $currentTime->setTimezone(new \DateTimeZone('UTC'));
                    $currentTime = $currentTime->format('Y-m-d H:i:s');
                    s::set('last_login', $currentTime);

                    // Set session array
                    $_SESSION = array(
                        'userid'           => $user->id,
                        'realname'         => $user->name,
                        'username'         => $user->username,
                        'email'            => $user->email,
                        'deposit'          => $user->deposit,
                        'type'             => $user->type,
                        'isAdmin'          => (in_array('Administrador', $groups)),
                        'isMaster'         => (in_array('Masters', $groups)),
                        'privileges'       => $privileges,
                        'accesscategories' => $user->getAccessCategoryIds(),
                        'updated'          => time(),
                        'session_lifetime' => $maxSessionLifeTime * 60,
                        'user_language'    => $user->getMeta('user_language'),
                        'csrf'             => md5(uniqid(mt_rand(), true)),
                        'meta'             => $user->getMeta(),
                    );

                    $forwardTo = $request->request->filter('forward_to', null, FILTER_SANITIZE_STRING);

                    return $this->redirect($forwardTo ?: SITE_URL_ADMIN);
                }

            } else {
                m::add(_('Username or password incorrect.'), m::ERROR);

                // Get setting var for count failed login attempts
                $failedLogin   = (s::get('failed_login_attempts')) ? s::get('failed_login_attempts') : 0;
                if (!s::get('failed_login_time')) {
                    $firstBadLogin = time();
                    s::set('failed_login_time', $firstBadLogin);
                } else {
                    $firstBadLogin = s::get('failed_login_time');
                }

                // First unsuccessful login
                if ((time() - $firstBadLogin) > $lockoutTime || $failedLogin == 0) {
                    s::set('failed_login_attempts', 1);
                    $failedLogin = 1;
                    // Set first failed login time
                    s::set('failed_login_time', time());
                } else {
                    // Count another bad login
                    $failedLogin = $failedLogin + 1;
                    s::set('failed_login_attempts', $failedLogin);
                }

                return $this->redirect($this->generateUrl('admin_login_form'));
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

            session_destroy();

            return $this->redirect($this->generateUrl('admin_login_form'));

        } else {
            return new Response('Are you hijacking my session dude?!');
        }
    }
}
