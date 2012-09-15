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
require_once SITE_VENDOR_PATH."/phpmailer/class.phpmailer.php";
require_once 'recaptchalib.php';

// Setup view
$tpl = new Template(TEMPLATE_USER);

// Fetch some code
$category_name = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

require_once 'statics_advertisement.php';

//If the form was sent
$action = $request->request->filter('action', null, FILTER_SANITIZE_STRING);

if (isset($action)
    && ($action == 'submit'
        || $action == 'create_subscriptor')
) {

    //Get config vars
    $configRecaptcha = s::get('recaptcha');
    $configSiteName = s::get('site_name');
    $configMailTo = s::get('newsletter_maillist');

    $recaptcha_challenge_field = $request->request->filter('recaptcha_challenge_field', null, FILTER_SANITIZE_STRING);
    $recaptcha_response_field = $request->request->filter('recaptcha_response_field', null, FILTER_SANITIZE_STRING);

    // Get reCaptcha validate response
    $resp = recaptcha_check_answer(
        $configRecaptcha['private_key'],
        $_SERVER["REMOTE_ADDR"],
        $recaptcha_challenge_field,
        $recaptcha_response_field
    );

    // What happens when the CAPTCHA was entered incorrectly
    if (!$resp->is_valid) {
        $resp = '<script>(!alert("The reCAPTCHA wasn\'t entered correctly. '
              . 'Go back and try it again."))</script>'
              . '<script>location.href="#"</script>';
        echo($resp);
    } else {
        // Correct CAPTCHA, bad mail and name empty

        $email = $request->request->filter('email', null, FILTER_SANITIZE_STRING);
        $name = $request->request->filter('name', null, FILTER_SANITIZE_STRING);

        if ( empty($email) || empty($name)) {
            $resp = '<script>(!alert("Lo sentimos, no se ha podido completar'
                . ' su solicitud.\nVerifique el formulario y vuelva intentarlo."))</script>'
                . '<script>location.href="#"</script>';
            echo ($resp);
        } else {
            // Correct CAPTCHA, correct mail and name not empty

            //Filter $_POST vars from FORM
            $data['name'] = $name;
            $data['email'] = $email;
            $data['subscription'] = $request->request->filter('subscription', null, FILTER_SANITIZE_STRING);
            $data['subscritorEntity'] = $request->request->filter('entity', null, FILTER_SANITIZE_STRING);
            $data['subscritorCountry'] = $request->request->filter('country', null, FILTER_SANITIZE_STRING);
            $data['subscritorCommunity'] = $request->request->filter('community', null, FILTER_SANITIZE_STRING);

            switch ($action) {
                // Logic for subscription sending a mail to s::get('newsletter_maillist')
                case 'submit':

                    //Build mail body
                    $formulario= "Nombre y Apellidos: ". $data['name']." \r\n".
                        "Email: ".$data['email']." \r\n";
                    if (!empty($data['subscritorEntity']) ) {
                        $formulario.= "Entidad: ".$data['subscritorEntity']." \n";
                    }
                    if (!empty($data['subscritorCountry']) ) {
                        $formulario.= "País: ".$data['subscritorCountry']." \n";
                    }
                    if (!empty($data['subscritorCommunity']) ) {
                        $formulario.= "Provincia de Origen: ".$data['subscritorCommunity']." \n";
                    }

                    // Checking the type of action to do (alta/baja)
                    if ($data['subscription'] == 'alta') {
                        $subject  = utf8_decode("Solicitud de ALTA - Boletín ".$configSiteName);

                        $body=  "Solicitud de Alta en el boletín de: \r\n". $formulario;

                        $resp = '<script language="JavaScript">(!alert("Se ha '
                              . 'subscrito correctamente al boletín."))</script>'
                              . '<script language="javascript">location.href="/home"</script>';
                    } else {
                        $subject  = utf8_decode("Solicitud de BAJA - Boletín ".$configSiteName);

                        $body=  "Solicitud de Baja en el boletín de: \r\n". $formulario;

                        $resp='<script>(!alert("Se ha dado de baja del boletín correctamente."))</script>
                                <script>location.href="/home"</script>';
                    }

                    //Send mail
                    $to=$configMailTo['subscription'];

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

                    $mail->Subject = $subject;
                    $mail->From = $data['email'];
                    $mail->FromName = utf8_decode($data['name']);
                    $mail->Body = utf8_decode($body);

                    $mail->AddAddress($to, $to);

                    if ($mail->Send()) {
                        echo($resp);
                    } else {
                        echo('<script>(!alert("Lo sentimos, no se ha podido '
                            .'completar su solicitud.\nVerifique el formulario '
                            .'y vuelva intentarlo."))</script>'
                            .'<script>location.href="/home"</script>');
                    }
                    break;
                case 'create_subscriptor':

                    if ($data['subscription'] == 'alta') {
                        $data['subscription'] = 1;
                        $data['status'] = 2;

                        $user = new Subscriptor();

                        if ($user->create($data)) {
                            echo('<script language="JavaScript">(!alert("Se ha '
                                .'subscrito correctamente al boletín."))</script>'
                                .'<script language="javascript">location.href="/home"</script>');
                        } else {
                            echo('<script>(!alert("Lo sentimos, no se ha podido '
                                .'completar su solicitud.\nVerifique el formulario '
                                .'y vuelva intentarlo."))</script>'
                                .'<script>location.href="/home"</script>');
                        }
                    } else {
                        $data['subscription'] = 0;
                        $data['status'] = 3;

                        $user = new Subscriptor();
                        $user = $user->getUserByEmail($data['email']);
                        $data['id'] = $user->id;

                        if ($user->update($data)) {
                            echo('<script language="JavaScript">(!alert("Se ha dado de baja correctamente."))</script>
                                        <script language="javascript">location.href="/home"</script>');
                        } else {
                            echo('<script>(!alert("Lo sentimos, no se ha podido '
                                .'completar su solicitud.\nVerifique el '
                                .'formulario y vuelva intentarlo."))</script>'
                                .'<script>location.href="/home"</script>');
                        }
                    }
                    break;
            }
        }
        $tpl->display('static_pages/subscription.tpl');
    }
    $tpl->display('static_pages/subscription.tpl');
}

$tpl->display('static_pages/subscription.tpl');

