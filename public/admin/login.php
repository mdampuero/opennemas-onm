<?php
/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('session_bootstrap.php');

require_once(SITE_CORE_PATH.'privileges_check.class.php');
require_once(SITE_CORE_PATH.'method_cache_manager.class.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('version', \Onm\Common\Version::VERSION);

$action = filter_input ( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
switch ($action) {
    case 'login':

		//  Get values from post
        $login    = filter_input ( INPUT_POST, 'login' , FILTER_SANITIZE_STRING );
        $password = filter_input ( INPUT_POST, 'password' , FILTER_SANITIZE_STRING );
        $token    = filter_input ( INPUT_POST, 'token' , FILTER_SANITIZE_STRING );
        $captcha  = '';

        $user = new User();
        // var_dump($_SESSION, $token);

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
                        'isAdmin'          => ( User_group::getGroupName($user->fk_user_group)=='Administrador' ),
                        'isMaster'         => ( User_group::getGroupName($user->fk_user_group)=='Masters' ),
                        'privileges'       => Privilege::get_privileges_by_user($user->id),
                        'accesscategories' => $user->get_access_categories_id(),
                        'authMethod'       => $user->authMethod,
                        'default_expire'   => $user->sessionexpire,
                        'csrf'             => md5(uniqid(mt_rand(), true))
                    );

                    // Store default expire time
                    $app->setcookie_secure('default_expire', $user->sessionexpire, 0, '/admin/');
                    Privileges_check::loadSessionExpireTime();
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
        $_SESSION['csrf'] = md5(uniqid(mt_rand(), true));
        $tpl->display('login/login.tpl');

    break;

    default:
        $_SESSION['csrf'] = md5(uniqid(mt_rand(), true));
        $tpl->display('login/login.tpl');
        break;
}
