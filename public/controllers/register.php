<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
use Onm\Settings as s;

// Setup app
require_once '../bootstrap.php';
require_once 'session_bootstrap.php';
require_once SITE_VENDOR_PATH.'/phpmailer/class.phpmailer.php';
require_once 'recaptchalib.php';

// Setup view
$tpl = new Template(TEMPLATE_USER);

require_once 'statics_advertisement.php';

//If the form was sent
$action = $request->request->filter('action', null, FILTER_SANITIZE_STRING);
if (empty($action)) {
    $action = $request->query->filter('action', null, FILTER_SANITIZE_STRING);
}

switch ($action) {
    case 'register':
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
        $resp = recaptcha_check_answer(
            $configRecaptcha['private_key'],
            $_SERVER["REMOTE_ADDR"],
            $recaptcha_challenge_field,
            $recaptcha_response_field
        );

        // What happens when the CAPTCHA was entered incorrectly
        if (!$resp->is_valid) {
            $tpl->assign('error', 'A comprobación do reCAPTCHA no es correcta. Ténteo de novo');
        } else {

            // Correct CAPTCHA - Filter $_POST vars from FORM
            $data['login']         = $request->request->filter('user_name', null, FILTER_SANITIZE_STRING);
            $data['name']          = $request->request->filter('full_name', null, FILTER_SANITIZE_STRING);
            $data['firstname']     = ''; // Unused field for frontend user
            $data['lastname']      = ''; // Unused field for frontend user
            $data['password']      = $request->request->filter('pwd', null, FILTER_SANITIZE_STRING);
            $data['cpwd']          = $request->request->filter('cpwd', null, FILTER_SANITIZE_STRING);
            $data['sessionexpire'] = 15;
            $data['email']         = $request->request->filter('user_email', null, FILTER_SANITIZE_EMAIL);
            $data['type']          = 1; // It is a frontend user registration.
            $data['token']         = md5(uniqid(mt_rand(), true)); // Token for activation
            $data['authorize']     = 0; // Before activation by mail, user is not allowed

            //Build mail body
            $mailSubject  = utf8_decode("Alta usuario - ".$configSiteName);
            $mailBody = "Estimad@ ". $data['name'] .", \r\n";
            $mailBody.= "Bienvenido a  ".$configSiteName.". ";
            $mailBody.= "Para activar su cuenta clica en el siguiente enlace:\r\n";
            $mailBody.= SITE_URL."activate/".$data['token']."/\r\n\n";
            $mailBody.= "Una vez activada, ";
            $mailBody.= "podrás modificar tus datos siempre que quieras en las opciones de usuario.\r\n";
            $mailBody.= "Gracias por formar parte de la comunidad de ".$configSiteName.".\r\n";
            $mailBody.= "Un saludo,\r\n";
            $mailBody.= "El equipo de ".$configSiteName;

            // Before send mail and create user on DB, do some checks
            $user = new User();

            // Check if pwd and cpwd are the same
            $isValidPass = ($data['password'] == $data['cpwd']);
            if (!$isValidPass) {
                $warningPass = 'Contrasinal e a confirmación teñen que ser iguales.';
                $tpl->assign('warning_pass', $warningPass);
            }

            // Check existing mail
            $mailAlreadyExists = $user->checkIfExistsFrontUserEmail($data['email']);
            if ($mailAlreadyExists) {
                $warningMail = 'O enderezo eletrónico xa está en uso.';
                $tpl->assign('warning_mail', $warningMail);
            }

            // Check existing user name
            $userNameAlreadyExists = $user->checkIfExistsFrontUserName($data['name']);
            if ($userNameAlreadyExists) {
                $warningUserName = 'O nome de usuario xa está en uso.';
                $tpl->assign('warning_user_name', $warningUserName);
            }

            // If checks are both false and pass is valid then send mail
            if (!$mailAlreadyExists && !$userNameAlreadyExists && $isValidPass) {
                $to = $data['email'];

                $mail = new PHPMailer();
                $mail->SetLanguage('es');
                $mail->IsSMTP();
                $mail->Host = MAIL_HOST;
                $mail->Username = MAIL_USER;
                $mail->Password = MAIL_PASS;

                if (!empty($mail->Username) && !empty($mail->Password)) {
                    $mail->SMTPAuth = true;
                } else {
                    $mail->SMTPAuth = false;
                }

                $mail->Subject = $mailSubject;
                $mail->From = "mailer@opennemas.com";
                $mail->FromName = $configSiteName;
                $mail->Body = utf8_decode($mailBody);

                $mail->AddAddress($to, $to);

                if ($mail->Send()) {
                    $sentMail = true;
                    if (!$user->create($data)) {
                        $tpl->assign(
                            'error',
                            'A ocurrido un erro. Intente completar o formulario con datos válidos.'
                        );
                    } else {
                        $tpl->assign(
                            'success',
                            'A sua conta xa está creada. Comprobe o seu correo para activala.'
                        );
                    }
                } else {
                    $sentMail = false;
                }
                $tpl->assign('sent_mail', $sentMail);
            }
        }

        $tpl->display('login/register.tpl');
        break;
    case 'activate':
        // When user confirms registration from email
        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);
        $captcha = '';
        $user = new User();
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
                $app::setCookieSecure('default_expire', $user->sessionexpire, 0);
                PrivilegesCheck::loadSessionExpireTime();
                $GLOBALS['Session']->cleanExpiredSessionFiles();
            }

            $paypalEmail = s::get("paypal_settings");
            $subscriptionItems = Kiosko::getSubscriptionItems();

            $tpl->assign(
                array(
                    'paypal_email' => $paypalEmail['email'],
                    'subscription_items' => $subscriptionItems,
                )
            );
            $tpl->display('login/deposit.tpl');
        } else {
            $tpl->assign('error', 'A ocurrido un erro na activación. Volva rexistrarse.');
            Application::forward(SITE_URL.'/register/');
        }
        break;
    case 'user_box':
        $tpl->display('login/user_box.tpl');
        break;
    case 'recover_pass':
        $tpl->display('login/recover_pass.tpl');
        break;
    case 'recover_send_mail':
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
        $resp = recaptcha_check_answer(
            $configRecaptcha['private_key'],
            $_SERVER["REMOTE_ADDR"],
            $recaptcha_challenge_field,
            $recaptcha_response_field
        );

        // What happens when the CAPTCHA was entered incorrectly
        // if (!$resp->is_valid) {
        //     echo('The reCAPTCHA wasn\'t entered correctly. Please try it again.');
        // } else {

        // Correct CAPTCHA - Filter $_POST vars from FORM
        $email = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);

        // Get user by email
        $user = new User();
        $userData = $user->getUserDataByEmail($email);


        // If e-mail exists in DB
        if (!empty($userData) && $userData['type'] == 1) {

            // Generate and update user with new token
            $token = md5(uniqid(mt_rand(), true));
            $user->updateUserToken($userData['pk_user'], $token);

            //Build mail body
            $mailSubject  = utf8_decode("Recordatorio de contrasinal - ".$configSiteName);
            $mailBody = "Estimad@ ".$userData['login'].", \r\n";
            $mailBody.= "Para  reestablecer su contraseña, por favor acceda a este enlace:\r\n";
            $mailBody.= SITE_URL."regenerate/pass/check/".$token."/\r\n\n";
            $mailBody.= "Podrás modificar tus datos siempre que desee, ";
            $mailBody.= "incluido la contraseña, en las opciones de usuario\r\n\n";
            $mailBody.= "Gracias por formar parte de la comunidad de ".$configSiteName.".\r\n\n";
            $mailBody.= "Un saludo,\r\n\n";
            $mailBody.= "El equipo de ".$configSiteName;

            $to = $email;

            $mail = new PHPMailer();
            $mail->SetLanguage('es');
            $mail->IsSMTP();
            $mail->Host = MAIL_HOST;
            $mail->Username = MAIL_USER;
            $mail->Password = MAIL_PASS;

            if (!empty($mail->Username) && !empty($mail->Password)) {
                $mail->SMTPAuth = true;
            } else {
                $mail->SMTPAuth = false;
            }

            $mail->Subject = $mailSubject;
            $mail->From = "mailer@opennemas.com";
            $mail->FromName = $configSiteName;
            $mail->Body = utf8_decode($mailBody);

            $mail->AddAddress($to, $to);

            if ($mail->Send()) {
                $success = 'Comprobe a bandexa de entrada do seu correo electrónico para cambiar a contrasinal.</br>';
                $success.= 'Esta páxina será redireccionada en 8 segundos.';
                $tpl->assign('success', $success);
            } else {
                $error = 'A ocurrido un erro. Por favor, ténteo de novo.';
                $tpl->assign('error', $error);
            }

            // Display form and assign flag to template
            $tpl->assign('mail_ok', true);

        } else {
            // If e-mail doesn't exists
            $error = 'O enderezo electrónico non se atopa nos nosos rexistros.';
            $tpl->assign('error', $error);
        }

        // Display form
        $tpl->display('login/recover_pass.tpl');

        break;
    case 'regenerate_pass_check':
        // Get token
        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);

        // Get user by token
        $user = new User();
        $userData = $user->getUserByToken($token);

        // If token is invalid redirect to index
        if (empty($userData)) {
            Application::forward(SITE_URL);
        }

        // Generate and update user with new token
        $newToken = md5(uniqid(mt_rand(), true));
        $user->updateUserToken($userData['pk_user'], $newToken);

        $tpl->assign('token', $newToken);
        $tpl->display('login/regenerate_pass.tpl');

        break;
    case 'regenerate_pass':
        // Get token
        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);
        $pass = $request->request->filter('pwd', null, FILTER_SANITIZE_STRING);
        $confPass = $request->request->filter('cpwd', null, FILTER_SANITIZE_STRING);
        $captcha = '';

        // Get user by token
        $user = new User();
        $userData = $user->getUserByToken($token);

        // If token is invalid redirect to index
        if (empty($userData) || $pass != $confPass || empty($pass)) {
            Application::forward(SITE_URL);
        } else {

            $user->updateUserPassword($userData['pk_user'], md5($pass));

            if ($user->login($userData['login'], $pass, $token, $captcha)) {
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
                $app::setCookieSecure('default_expire', $user->sessionexpire, 0);
                PrivilegesCheck::loadSessionExpireTime();
                $GLOBALS['Session']->cleanExpiredSessionFiles();

                $success = 'Modificada con éxito a contrasinal. A páxina será redireccionada en unos segundos';
                $tpl->assign('success', $success);
                $tpl->display('login/regenerate_pass.tpl');
            }
        }


        $tpl->assign('token', $token);
        $tpl->display('login/regenerate_pass.tpl');

        break;
    case 'edit_user':
        // Get user id from GET
        $userId = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

        if ($_SESSION['userid'] == $userId) {
            $user = new User($userId);

            // Generate token for security
            $token = md5(uniqid(mt_rand(), true));

            // Assign data to form and display
            $tpl->assign('user', $user);
            $tpl->assign('token', $token);
            $tpl->display('login/register.tpl');

        } else {
            // If actual session user is not the requested user
            Application::forward(SITE_URL);
        }

        break;
    case 'save-edit-user':
        // Get variables from GET
        $userId = $request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $token  = $request->query->filter('token', null, FILTER_SANITIZE_STRING);

        // Get variables from the user FORM
        $data['login']    = $request->request->filter('user_name', null, FILTER_SANITIZE_STRING);
        $data['name']     = $request->request->filter('full_name', null, FILTER_SANITIZE_STRING);
        $data['password'] = $request->request->filter('pwd', '', FILTER_SANITIZE_STRING);
        $data['cpwd']     = $request->request->filter('cpwd', '', FILTER_SANITIZE_STRING);
        $data['email']    = $request->request->filter('user_email', null, FILTER_SANITIZE_EMAIL);

        // Flag to check if response is ok and redirect user from template
        $resp = false;
        if ($userId == $_SESSION['userid']) {
            // Get user data, check token and confirm pass
            $user = new User($userId);
            if ($user->getUserByToken($token) != 0
                && $data['password'] == $data['cpwd']) {

                if ($user->update($data)) {
                    $resp = true;
                    $tpl->assign('success', 'Os datos do usuario foron modificados correctamente');
                } else {
                    $error = 'A ocurrido un erro ao gardar os datos. Por favor, ténteo de novo.';
                    $tpl->assign('error', $error);
                }
            } else {
                $error = 'A ocurrido un erro de seguridade. Por favor, ténteo de novo.';
                $tpl->assign('error', $error);
            }
        } else {
            $error = 'A ocurrido un erro co usuario. Por favor, ténteo de novo.';
            $tpl->assign('error', $error);
        }

        $tpl->assign('resp_ok', $resp);
        $tpl->display('login/register.tpl');

        break;
    default:

        $tpl->display('login/register.tpl');

        break;
}

