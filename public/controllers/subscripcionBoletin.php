<?php
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

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
$ccm = ContentCategoryManager::get_instance();
$cm = new ContentManager();
require_once ("index_sections.php");
/******************************  STATIC PAGES  *********************************/
require_once("widget_static_pages.php");
/******************************  STATIC PAGES  *********************************/
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

/********************************* ADVERTISEMENTS  *********************************************/
require_once ("statics_advertisement.php");
/********************************* ADVERTISEMENTS  *********************************************/

$action = (isset($_REQUEST['action']))? $_REQUEST['action']: null;

switch($action) {
    case 'submit':
            $resp = recaptcha_check_answer (RECAPTCHA_PRIVATE_KEY,
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $_POST["recaptcha_response_field"]);

            if (!$resp->is_valid) {
                // What happens when the CAPTCHA was entered incorrectly
                $resp='<script language="JavaScript">(!alert("The reCAPTCHA wasn\'t entered correctly. Go back and try it again."))</script>
                            <script language="javascript">location.href="#"</script>';
                echo ($resp);
                break;
            } else {                      
                if(!filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) || $_REQUEST['name']==''){
                    $resp='<script language="JavaScript">(!alert("Lo sentimos, no se ha podido completar su solicitud.\nVerifique el formulario y vuelva intentarlo."))</script>
                            <script language="javascript">location.href="#"</script>';
                    echo ($resp);
                    break;
                }
                
                // Form data
                $formulario= "Nombre y Apellidos: ". $_POST['name']." \r\n".
                    "Email: ".$_POST['email']." \r\n";
                if (!empty($_POST['entity']) ) {$formulario.= "Entidad ".$_POST['entity']." \n"; }
                if (!empty($_POST['country']) ) {$formulario.= "País ".$_POST['country']." \n"; }
                if (!empty($_POST['community']) ) {$formulario.= "Provincia de Origen ".$_POST['community']." \n"; }
                
                // Checking the type of action to do (alta/baja)                
                if($_REQUEST['boletin']=='alta'){
                    $subject  = utf8_decode("Solicitud de ALTA - Boletín ".SITE_FULLNAME);

                    $body=  "Solicitud de Alta en el boletín de: \r\n". $formulario;

                    $resp='<script language="JavaScript">(!alert("Se ha subscrito correctamente al boletín."))</script>
                            <script language="javascript">location.href="/home"</script>';
                }else{
                    $subject  = utf8_decode("Solicitud de BAJA - Boletín ".SITE_FULLNAME);

                    $body=  "Solicitud de Baja en el boletín de: \r\n". $formulario;

                    $resp='<script language="JavaScript">(!alert("Se ha dado de baja del boletín correctamente."))</script>
                            <script language="javascript">location.href="/home"</script>';
                }

                $to=MAIL_FORM_RECAPTCHA;


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
                $mail->From = $_REQUEST['email'];
                $mail->FromName = utf8_decode($_REQUEST['name']);
                $mail->Body = utf8_decode($body);

                $mail->AddAddress($to, $to);


                if($mail->Send())
                {
                    echo($resp);
                }
                else {
                    echo('<script language="JavaScript">(!alert("Lo sentimos, no se ha podido completar su solicitud.\nVerifique el formulario y vuelva intentarlo."))</script>
                    <script language="javascript">location.href="/home"</script>');
                }
            }
            break;
}
$tpl->display('static_pages/suscripcion.tpl');
