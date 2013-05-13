<?php
/**
 * Handles the actions for the user profile
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

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the user profile
 *
 * @package Frontend_Controllers
 **/
class UserController extends Controller
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

        require_once SITE_VENDOR_PATH.'/phpmailer/class.phpmailer.php';
        require_once 'recaptchalib.php';
    }

    /**
     * Shows the user information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        if (array_key_exists('userid', $_SESSION) && !empty($_SESSION['userid'])) {
            $user = new \User($_SESSION['userid']);

            return $this->render(
                'user/show.tpl',
                array(
                    'user'  => $user
                )
            );
        }

        return $this->redirect($this->generateUrl('frontend_auth_login'));
    }



    /**
     * Handles the registration of a new user in frontend
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function registerAction(Request $request)
    {
        //Get config vars
        $configRecaptcha = s::get('recaptcha');
        $configSiteName = s::get('site_name');

        $recaptcha_challenge_field = $request->request->filter(
            'recaptcha_challenge_field',
            null,
            FILTER_SANITIZE_STRING
        );
        $recaptcha_response_field = $request->request->filter(
            'recaptcha_response_field',
            null,
            FILTER_SANITIZE_STRING
        );

        // Get reCaptcha validate response
        $resp = \recaptcha_check_answer(
            $configRecaptcha['private_key'],
            $_SERVER["REMOTE_ADDR"],
            $recaptcha_challenge_field,
            $recaptcha_response_field
        );

        $errors = array();
        // What happens when the CAPTCHA was entered incorrectly
        if ('POST' != $request->getMethod()) {
            // Do nothing
        } elseif (!$resp->is_valid) {
            $errors []= _('Verification image not valid. Try to fill it again.');
        } else {
            // Correct CAPTCHA - Filter $_POST vars from FORM
            $data = array(
                'authorize'     => 0, // Before activation by mail, user is not allowed
                'cpwd'          => $request->request->filter('cpwd', null, FILTER_SANITIZE_STRING),
                'email'         => $request->request->filter('user_email', null, FILTER_SANITIZE_EMAIL),
                'login'         => $request->request->filter('user_name', null, FILTER_SANITIZE_STRING),
                'name'          => $request->request->filter('full_name', null, FILTER_SANITIZE_STRING),
                'password'      => $request->request->filter('pwd', null, FILTER_SANITIZE_STRING),
                'sessionexpire' => 15,
                'token'         => md5(uniqid(mt_rand(), true)), // Token for activation,
                'type'          => 1, // It is a frontend user registration.
                'id_user_group' => null,
            );

            // Before send mail and create user on DB, do some checks
            $user = new \User();

            // Check if pwd and cpwd are the same
            if (($data['password'] != $data['cpwd'])) {
                $errors []= _('Password and confirmation must be equal.');
            }

            // Check existing mail
            if ($user->checkIfExistsUserEmail($data['email'])) {
                $errors []= _('The email address is already in use.');
            }

            // Check existing user name
            if ($user->checkIfExistsUserName($data['login'])) {
                $errors []= _('The user name is already in use.');
            }

            // If checks are both false and pass is valid then send mail
            if (count($errors) <= 0) {

                $url = $this->generateUrl('frontend_user_activate', array('token' => $data['token']), true);

                $tplMail = new \Template(TEMPLATE_USER);
                $tplMail->caching = 0;
                $mailSubject = sprintf(_('New user account in %s'), s::get('site_title'));
                $mailBody = $tplMail->fetch(
                    'user/emails/register.tpl',
                    array(
                        'name' => $data['name'],
                        'url'  => $url,
                    )
                );

                // Build the message
                $message = \Swift_Message::newInstance();
                $message
                    ->setSubject($mailSubject)
                    ->setBody($mailBody, 'text/plain')
                    ->setTo($user->email)
                    ->setFrom(array('no-reply@postman.opennemas.com' => s::get('site_name')));

                // If user is successfully created, send an email
                if (!$user->create($data)) {
                    $errors []=_('An error has occurred. Try to complete the form with valid data.');
                } else {
                    try {
                        $mailer = $this->get('mailer');
                        $mailer->send($message);

                        $this->view->assign('mailSent', true);
                    } catch (\Exception $e) {
                        // Log this error
                        $this->get('logger')->notice(
                            "Unable to send the user activation email for the "
                            ."user {$user->id}: ".$e->getMessage()
                        );

                        m::add(_('Unable to send your recover password email. Please try it later.'), m::ERROR);
                    }
                    // Set registration date
                    $user->addRegisterDate();
                    $this->view->assign(
                        'success',
                        _('Your account is now set up. Check your email to activate.')
                    );
                }
            }
        }

        return $this->render(
            'authentication/register.tpl',
            array(
                'errors' => $errors,
            )
        );
    }

    /**
     * Updates the user data
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        if (!isset($_SESSION['userid'])) {
            return $this->redirect($this->generateUrl('frontend_auth_login'));
        }

        // Get variables from the user FORM
        $data['login']    = $request->request->filter('username', null, FILTER_SANITIZE_STRING);
        $data['name']     = $request->request->filter('name', null, FILTER_SANITIZE_STRING);
        $data['email']    = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);
        $data['password'] = $request->request->filter('pwd', '', FILTER_SANITIZE_STRING);
        $data['password-verify']     = $request->request->filter('password-verify', '', FILTER_SANITIZE_STRING);

        if ($data['password'] != $data['password-verify']) {
            return $this->redirect($this->generateUrl('frontend_auth_login'));
        }
        // Get user data, check token and confirm pass
        $user = new \User($userId);
        if ($user->id <= 0) {
            if ($user->update($data)) {
                m::add('Data updated successfully', m::SUCCESS);
            } else {
                m::add('There was an error while updating the user data.', m::ERROR);
            }
        } else {
            m::add('The user does not exists.', m::ERROR);
        }

        return $this->redirect($this->generateUrl('frontend_user_show'));
    }

    /**
     * Shows the user box
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function userBoxAction(Request $request)
    {
        return $this->render('login/user_box.tpl');
    }

    /**
     * Activates an user account given an token
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function activateAction(Request $request)
    {
        // When user confirms registration from email
        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);
        $captcha = '';
        $user = new \User();
        $userData = $user->getUserByToken($token);

        if ($userData) {
            $user->authorizeUser($userData['pk_user']);

            if ($user->login($userData['login'], $userData['password'], $userData['token'], $captcha)) {
                // Increase security by regenerating the id
                session_regenerate_id();

                $_SESSION = array(
                    'userid'           => $user->id,
                    'realname'         => $user->name,
                    'username'         => $user->login,
                    'email'            => $user->email,
                    'deposit'          => $user->deposit,
                    'authMethod'       => $user->authMethod,
                    'default_expire'   => $user->sessionexpire,
                    'csrf'             => md5(uniqid(mt_rand(), true))
                );

                // Store default expire time
                \Application::setCookieSecure('default_expire', $user->sessionexpire, 0);
                PrivilegesCheck::loadSessionExpireTime();
            }

            $paypalEmail = s::get("paypal_settings");
            $subscriptionItems = \Kiosko::getSubscriptionItems();

            $this->view->assign(
                array(
                    'paypal_email'       => $paypalEmail['email'],
                    'subscription_items' => $subscriptionItems,
                )
            );

            return $this->render('login/deposit.tpl');
        } else {
            m::add(_('There was an error while creating your user account. Please try again'), m::ERROR);

            return $this->redirect($this->generateUrl('frontend_user_register'));
        }
    }

    /**
     * Shows the form for recovering the pass of a user and
     * sends the mail to the user
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function recoverPasswordAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            return $this->render('user/recover_pass.tpl');
        } else {
            $email = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);

            // Get user by email
            $user = new \User();
            $user->findByEmail($email);

            // If e-mail exists in DB
            if (($user !== false)) {
                // Generate and update user with new token
                $token = md5(uniqid(mt_rand(), true));
                $user->updateUserToken($user->id, $token);

                $url = $this->generateUrl('frontend_user_resetpass', array('token' => $token), true);

                $tplMail = new \Template(TEMPLATE_USER);
                $tplMail->caching = 0;

                $mailSubject = sprintf(_('Password reminder for %s'), s::get('site_title'));
                $mailBody = $tplMail->fetch(
                    'user/emails/recoverpassword.tpl',
                    array(
                        'user' => $user,
                        'url'  => $url,
                    )
                );

                //  Build the message
                $message = \Swift_Message::newInstance();
                $message
                    ->setSubject($mailSubject)
                    ->setBody($mailBody, 'text/plain')
                    ->setTo($user->email)
                    ->setFrom(array('no-reply@postman.opennemas.com' => s::get('site_name')));

                try {
                    $mailer = $this->get('mailer');
                    $mailer->send($message);

                    $this->view->assign('mailSent', true);
                } catch (\Exception $e) {
                    // Log this error
                    $this->get('logger')->notice(
                        "Unable to send the recover password email for the "
                        ."user {$user->id}: ".$e->getMessage()
                    );

                    m::add(_('Unable to send your recover password email. Please try it later.'), m::ERROR);
                }

            } else {
                m::add(_('Unable to find an user with that email.'), m::ERROR);
            }

            // Display form
            return $this->render('user/recover_pass.tpl');
        }
    }

    /**
     * Regenerates the pass for a user
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function regeneratePasswordAction(Request $request)
    {
        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);

        $user = new \User();
        $user = $user->findByToken($token);

        if ('POST' !== $request->getMethod()) {
            if (empty($user->id)) {
                m::add(
                    _(
                        'Unable to find the password reset request. '
                        .'Please check the url we sent you in the email.'
                    ),
                    m::ERROR
                );
                $this->view->assign('userNotValid', true);
            } else {
                $this->view->assign(
                    array(
                        'user' => $user
                    )
                );
            }
        } else {
            $password       = $request->request->filter('password', null, FILTER_SANITIZE_STRING);
            $passwordVerify = $request->request->filter('password-verify', null, FILTER_SANITIZE_STRING);

            if ($password == $passwordVerify && !empty($password)) {
                $user->updateUserPassword($user->id, $password);
                $user->updateUserToken($user->id, null);

                $this->view->assign('updated', true);
            } else {
                m::add(_('Unable to find the password reset request. Please check the url we sent you in the email.'));
            }

        }

        return $this->render('user/regenerate_pass.tpl', array('token' => $token, 'user' => $user));

    }

    /**
     * Generates the HTML for the user menu by ajax
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function getUserMenuAction(Request $request)
    {
        $login = $this->generateUrl('frontend_auth_login');
        $logout = $this->generateUrl('frontend_auth_logout');
        $register = $this->generateUrl('frontend_user_register');
        $myAccount = $this->generateUrl('frontend_user_show');

        if (isset($_SESSION['userid'])) {
            $output =
                '<ul>
                    <li>
                        <a href="'.$logout.'">'._("Logout").'</a>
                    </li>
                    <li>
                        <a href="'.$myAccount.'">'._("My account").'</a>
                    </li>
                </ul>';
        } else {
            $output =
                '<ul>
                    <li>
                        <a href="'.$register.'">'._("Register").'</a>
                    </li>
                    <li>
                        <a href="'.$login.'">'._("Login").'</a>
                    </li>
                </ul>';
        }

        return $output;
    }
}
