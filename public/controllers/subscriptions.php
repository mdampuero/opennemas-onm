<?php

use Onm\Settings as s;
/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('../libs/phpmailer/class.phpmailer.php');
require_once('recaptchalib.php');

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);

/**
 * Initialize contents manager
*/
$ccm = ContentCategoryManager::get_instance();
$cm = new ContentManager();

/**
 * Fetch some code 
*/
require_once("index_sections.php");
require_once("widget_static_pages.php");
require_once("statics_advertisement.php");

//If the form was sent
$action = (isset($_POST['action']))? $_POST['action']: null;
if (isset($action) && !empty($action)
        && ($action == 'submit' || $action == 'create_subscriptor')
    ) 
{

    //Get config vars
    $configRecaptcha = s::get('recaptcha');
    $configSiteName = s::get('site_name');
    $configMailTo = s::get('newsletter_maillist');

    // Get reCaptcha validate response
    $resp = recaptcha_check_answer ($configRecaptcha['private_key'],
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);

    // What happens when the CAPTCHA was entered incorrectly
    if (!$resp->is_valid) {
        $resp='<script>(!alert("The reCAPTCHA wasn\'t entered correctly. Go back and try it again."))</script>
                    <script>location.href="#"</script>';
        echo ($resp);
    } else {// Correct CAPTCHA, bad mail and name empty
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || $_POST['name']==''){
            $resp='<script>(!alert("Lo sentimos, no se ha podido completar su solicitud.\nVerifique el formulario y vuelva intentarlo."))</script>
                    <script>location.href="#"</script>';
            echo ($resp);
        } else {// Correct CAPTCHA, correct mail and name not empty

            //Filter $_POST vars from FORM
            $data['name'] = filter_input( INPUT_POST, 'name' , FILTER_SANITIZE_STRING, array('options' => array('default' => null)) );
            $data['email'] = filter_input( INPUT_POST, 'email' , FILTER_SANITIZE_EMAIL, array('options' => array('default' => null)) );
            $data['subscription'] = filter_input( INPUT_POST, 'subscription' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'alta')) );
            $data['subscritorEntity'] = filter_input( INPUT_POST, 'entity' , FILTER_SANITIZE_STRING, array('options' => array('default' => null)) );
            $data['subscritorCountry'] = filter_input( INPUT_POST, 'country' , FILTER_SANITIZE_STRING, array('options' => array('default' => null)) );
            $data['subscritorCommunity'] = filter_input( INPUT_POST, 'community' , FILTER_SANITIZE_STRING, array('options' => array('default' => null)) );

            switch($action) {
                // Logic for subscription sending a mail to s::get('newsletter_maillist')
                case 'submit':

                    //Build mail body
                    $formulario= "Nombre y Apellidos: ". $data['name']." \r\n".
                        "Email: ".$data['email']." \r\n";
                    if (!empty($data['subscritorEntity']) ) {$formulario.= "Entidad: ".$data['subscritorEntity']." \n"; }
                    if (!empty($data['subscritorCountry']) ) {$formulario.= "País: ".$data['subscritorCountry']." \n"; }
                    if (!empty($data['subscritorCommunity']) ) {$formulario.= "Provincia de Origen: ".$data['subscritorCommunity']." \n"; }

                    // Checking the type of action to do (alta/baja)                
                    if($data['subscription'] == 'alta'){
                        $subject  = utf8_decode("Solicitud de ALTA - Boletín ".$configSiteName);

                        $body=  "Solicitud de Alta en el boletín de: \r\n". $formulario;

                        $resp='<script language="JavaScript">(!alert("Se ha subscrito correctamente al boletín."))</script>
                                <script language="javascript">location.href="/home"</script>';
                    }else{
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

                    if (!empty($mail->Username) && !empty($mail->Password))
                    {
                        $mail->SMTPAuth = true;
                    } else {
                        $mail->SMTPAuth = false;
                    }

                    $mail->Subject = $subject;
                    $mail->From = $data['email'];
                    $mail->FromName = utf8_decode($data['name']);
                    $mail->Body = utf8_decode($body);

                    $mail->AddAddress($to, $to);


                    if($mail->Send()) {
                        echo($resp);
                    } else {
                        echo('<script>(!alert("Lo sentimos, no se ha podido completar su solicitud.\nVerifique el formulario y vuelva intentarlo."))</script>
                        <script>location.href="/home"</script>');
                    }

                break;

                case 'create_subscriptor':
                    if ($data['subscription'] == 'alta') {
                        $data['subscription'] = 1;
                        $data['status'] = 2;
                        
                        $user = new Subscriptor();
                        if($user->create( $data )) {
                            echo('<script language="JavaScript">(!alert("Se ha subscrito correctamente al boletín."))</script>
                                        <script language="javascript">location.href="/home"</script>');
                        } else {
                            echo('<script>(!alert("Lo sentimos, no se ha podido completar su solicitud.\nVerifique el formulario y vuelva intentarlo."))</script>
                                <script>location.href="/home"</script>');
                        }
                    } else {
                        $data['subscription'] = 0;
                        $data['status'] = 3;
                                                
                        $user = new Subscriptor();
                        $user = $user->getUserByEmail($data['email']);
                        $data['id'] = $user->id;
                        if($user->update( $data )) {
                            echo('<script language="JavaScript">(!alert("Se ha dado de baja correctamente."))</script>
                                        <script language="javascript">location.href="/home"</script>');
                        } else {
                            echo('<script>(!alert("Lo sentimos, no se ha podido completar su solicitud.\nVerifique el formulario y vuelva intentarlo."))</script>
                                <script>location.href="/home"</script>');
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
