<?php
/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('version', \Onm\Common\Version::VERSION);

$action = filter_input ( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
switch ($action) {
    case 'login':

		//  Get values from post
        $login    = $request->request->filter('login', null, FILTER_SANITIZE_STRING);
        $password = $request->request->filter('password', null, FILTER_SANITIZE_STRING);
        $token    = $request->request->filter('token', null, FILTER_SANITIZE_STRING);
        $captcha  = '';

        $user = new User();

        if ($_SESSION['csrf'] !== $token) {
            $tpl->assign('message', _('Login token is not valid. Try to autenticate again.'));
        } else {

            // Try to autenticate the user
            if ($user->login($login, $password, $token, $captcha)) {

                // Check if user account is activated
                if ($user->authorize != 1) {
                    $tpl->assign('message', _('This user was deactivated. Please ask your administrator.'));
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
                        'isAdmin'          => ( UserGroup::getGroupName($user->fk_user_group)=='Administrador' ),
                        'isMaster'         => ( UserGroup::getGroupName($user->fk_user_group)=='Masters' ),
                        'privileges'       => Privilege::get_privileges_by_user($user->id),
                        'accesscategories' => $user->get_access_categories_id(),
                        'authMethod'       => $user->authMethod,
                        'default_expire'   => $user->sessionexpire,
                        'csrf'             => md5(uniqid(mt_rand(), true))
                    );

                    // Store default expire time
                    $app::setCookieSecure('default_expire', $user->sessionexpire, 0, '/admin/');
                    PrivilegesCheck::loadSessionExpireTime();
                    $GLOBALS['Session']->cleanExpiredSessionFiles();

                    $forwardTo = filter_input(INPUT_POST, 'forward_to');
                    if (!is_null($forwardTo) && !empty($forwardTo)) {
                        Application::forward(SITE_URL.$forwardTo);
                    } else {
                        Application::forward(SITE_URL_ADMIN);
                    }
                }

            } else {
                $tpl->assign('message', _('Username or password incorrect.'));
            }
        }
        $token = md5(uniqid(mt_rand(), true));
        $tpl->assign('token', $token);
        $_SESSION['csrf'] = $token;

        $tpl->display('login/login.tpl');

    break;

    default:
        $token = md5(uniqid(mt_rand(), true));
        $tpl->assign('token', $token);
        $_SESSION['csrf'] = $token;
        $languages = Application::getAvailableLanguages();
        $currentLanguage =Application::$language;
        $tpl->assign('languages', $languages);
        $tpl->assign('current_language', $currentLanguage);
        $tpl->display('login/login.tpl');
        break;
}
