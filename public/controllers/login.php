<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
// Setup app
require_once '../bootstrap.php';
require_once 'session_bootstrap.php';

// Setup view
$tpl = new Template(TEMPLATE_USER);

//If the form was sent
$action = $request->request->filter('action', null, FILTER_SANITIZE_STRING);
if (empty($action)) {
    $action = $request->query->filter('action', null, FILTER_SANITIZE_STRING);
}


switch ($action) {
    case 'login':

        //  Get values from post
        $login    = $request->request->filter('login', null, FILTER_SANITIZE_STRING);
        $password = $request->request->filter('pwd', null, FILTER_SANITIZE_STRING);
        $token    = $request->request->filter('token', null, FILTER_SANITIZE_STRING);
        $captcha  = '';

        $user = new User();

        $isLoginCorrect = false;
        if ($_SESSION['csrf'] !== $token) {
            $tpl->assign('message', _('Login token is not valid. Try to autenticate again.'));
        } else {

            // Try to autenticate the user, not accept credencials from backend
            if ($user->login($login, $password, $token, $captcha) && $user->type == 1) {

                // Check if user account is activated
                if ($user->authorize != 1) {
                    $tpl->assign(
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
                        'authMethod'       => $user->authMethod,
                        'default_expire'   => $user->sessionexpire,
                        'csrf'             => md5(uniqid(mt_rand(), true))
                    );

                    // Store default expire time
                    $app::setCookieSecure('default_expire', $user->sessionexpire, 0, '/');
                    PrivilegesCheck::loadSessionExpireTime();

                    $isLoginCorrect = true;
                    $tpl->assign('success', _('You have logged in correctly. You will be redirect to the Home page'));
                    $tpl->assign('login_ok', $isLoginCorrect);
                }

            } else {
                $tpl->assign('message', _('Username or password incorrect.'));
            }
        }
        $token = md5(uniqid(mt_rand(), true));
        $tpl->assign('token', $token);
        $tpl->assign('login_ok', $isLoginCorrect);
        $_SESSION['csrf'] = $token;
        $tpl->display('login/login_form.tpl');

        break;
    default:
        $token = md5(uniqid(mt_rand(), true));
        $tpl->assign('token', $token);
        $_SESSION['csrf'] = $token;
        $tpl->display('login/login_form.tpl');
        break;
}

