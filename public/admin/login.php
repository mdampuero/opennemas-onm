<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');


require_once(SITE_CORE_PATH.'privileges_check.class.php');
require_once(SITE_CORE_PATH.'method_cache_manager.class.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

if( isset($_REQUEST['action'])){
	switch($_REQUEST['action']) {
        case 'login':

            $user = new User();

			/**
			 * Get values from post
			*/
			$token = filter_input ( INPUT_POST, 'token' , FILTER_SANITIZE_STRING );
			$captcha = filter_input ( INPUT_POST, 'captcha' , FILTER_SANITIZE_STRING );
			$login = filter_input ( INPUT_POST, 'login' , FILTER_SANITIZE_STRING );
			$password = filter_input ( INPUT_POST, 'password' , FILTER_SANITIZE_STRING );

            $result = $user->login($login, $password, $token, $captcha);

            if ($result === true) {

				$rememberme = filter_input ( INPUT_POST, 'rememberme' , FILTER_SANITIZE_STRING );

                if( isset($_REQUEST['rememberme']) ) {

                    $app->setcookie_secure("login_username", $login,    time()+60*60*24*30, '/admin/');
                    $app->setcookie_secure("login_password", $password, time()+60*60*24*30, '/admin/');

                } else {
                    if (isset($_COOKIE['login_username'])) {
                        // Caducar a cookie
                        setcookie("login_username", '', time()-(60*60) );
                        setcookie("login_password", '', time()-(60*60) );
                    }
                }

                /**
                 * Check if user account is activated
                 */
                if($user->authorize == 1){
                    // Load session
                    require_once('session_bootstrap.php');
                    
                    //Delete the cache that handles the number of active sessions
                    apc_delete(APC_PREFIX ."_"."num_sessions");

                    $_SESSION = array(
                        'userid' 			 => $user->id,
                        'username' 		 => $user->login,
                        'email' 			 => $user->email,
                        'isAdmin' 		 =>  ( User_group::getGroupName($user->fk_user_group)=='Administrador' ),
                        'isMaster' 		 =>  ( User_group::getGroupName($user->fk_user_group)=='Masters' ),
                        'privileges' 		 => Privilege::get_privileges_by_user($user->id),
                        'accesscategories' => $user->get_access_categories_id(),
                        'authMethod' 		 => $user->authMethod,
                        'default_expire'    => $user->sessionexpire,
                    );

                    /**
                     * Available authentication methods:  database, google_clientlogin
                     * Check if user auth is google_clientlogin stablish its auth for Gmail
                     */
                    if($user->authMethod == 'google_clientlogin') {
                            $_SESSION['authGmail']  = base64_encode($login.':'.$password);
                    }

                     /**
                     * Store default expire time
                     */
                    $app->setcookie_secure('default_expire', $user->sessionexpire, 0, '/admin/');

                    Privileges_check::loadSessionExpireTime();

                    //initHandleErrorPrivileges();
                    Application::forward(SITE_URL_ADMIN.SS.'index.php');
                } else{
                    $tpl->assign('message', _('This user was deactivated. Please ask your administrator.'));
                }
            } else {

                // Show google captcha
                if(isset($result['token'])) {
                    $tpl->assign('token', $result['token']);
                    $tpl->assign('captcha', $result['captcha']);
                }

                $tpl->assign('message', _('Username or password incorrect.'));
            }
        break;
	}
}
$tpl->assign('version', \Onm\Common\Version::VERSION);
$tpl->display('login/login.tpl');
