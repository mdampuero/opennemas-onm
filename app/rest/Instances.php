<?php

require_once SITE_VENDOR_PATH.'/swiftmailer/swiftmailer/lib/swift_required.php';

class Instances extends RestBase
{
    public $restler;
    private $mailer;

    /*
    * @url GET /instances/name/
    */
    public function name()
    {
        return INSTANCE_UNIQUE_NAME;
    }

    /*
    * @url GET /instances/mediaurl/
    */
    public function mediaUrl()
    {
        return MEDIA_IMG_PATH_WEB;
    }

    /*
    * @url GET /instances/sitepath/
    */
    public function siteUrl()
    {
        return SITE_URL;
    }

    /*
    * @url GET /instances/mediaimgpath/
    */
    public function mediaImgPath()
    {
        return MEDIA_IMG_PATH;
    }

    /*
    * @url GET /instances/instancemedia/
    */
    public function instanceMedia()
    {
        return INSTANCE_MEDIA;
    }

    /*
    * @url POST /instances/create/
    */
    protected function postCreate(
        $site_name,
        $internal_name,
        $domains,
        $user_name,
        $password,
        $contact_mail,
        $contact_IP,
        $timezone,
        $token,
        $companyMail,
        $domain
    ) {
        //Force internal_name lowercase
        //If is creating a new instance, get DB params on the fly
        $internalNameShort = filter_var(trim(substr($internal_name, 0, 11)), FILTER_SANITIZE_STRING);

        $settings = array(
            'TEMPLATE_USER' => "default",
            'MEDIA_URL'     => "http://media.opennemas.com",
            'BD_TYPE'       => "mysqli",
            'BD_HOST'       => "localhost",
            'BD_USER'       => $internalNameShort,
            'BD_PASS'       => \Onm\StringUtils::generatePassword(16),
            'BD_DATABASE'   => $internalNameShort,
            'TOKEN'         => filter_var($token, FILTER_SANITIZE_STRING),
        );

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone("UTC"));

        //Get all the Post data
        $data = array(
            'contact_IP'    => filter_var($contact_IP, FILTER_SANITIZE_STRING),
            'name'          => filter_var($site_name, FILTER_SANITIZE_STRING),
            'user_name'     => filter_var($user_name, FILTER_SANITIZE_STRING),
            'user_mail'     => filter_var($contact_mail, FILTER_SANITIZE_STRING),
            'user_pass'     => filter_var($password, FILTER_SANITIZE_STRING),
            'internal_name' => filter_var($internal_name, FILTER_SANITIZE_STRING),
            'domains'       => filter_var($domains, FILTER_SANITIZE_STRING),
            'activated'     => 1,
            'settings'      => $settings,
            'site_created'  => $date->format('Y-m-d H:i:s'),
        );

        // Also get timezone if comes from openhost form
        if (!empty ($timezone)) {
            $allTimezones = \DateTimeZone::listIdentifiers();
            foreach ($allTimezones as $key => $value) {
                if ($timezone == $value) {
                    $data['timezone'] = $key;
                }
            }
        }

        $errors = array();
        $im = Onm\Instance\InstanceManager::getInstance();
        // Check for reapeted internalnameshort and if so, add a number at the end
        $data = $im->checkInternalShortName($data);
        $errors = $im->create($data);

        if (is_array($errors) ) {
            return $errors;
        }

        $companyMail = filter_var($companyMail, FILTER_SANITIZE_STRING);
        $domain = filter_var($domains, FILTER_SANITIZE_STRING);
        $this->sendMails($data, $companyMail, $domain);

        return true;
    }

    private function sendMails($data, $companyMail, $domain)
    {
        $transport = \Swift_MailTransport::newInstance();
        $this->mailer = \Swift_Mailer::newInstance($transport);
        $this->sendMailToUser($data, $companyMail, $domain);
        $this->sendMailToCompany($data, $companyMail, $domain);
    }

    private function sendMailToUser($data, $companyMail, $domain)
    {
        $message = \Swift_Message::newInstance();
        $message->setTo(
            array($data['user_mail'] => $data['user_name'])
        );
        $message->setSubject("{$data['name']} ya está online");
        $body = "Bienvenido a OpenNemas\r\n";
        $body .= "\r\n";
        $body .= "Tu instancia de OpenNemas ya está disponible.\r\n";
        $body .= "Sus datos son los siguientes:\r\n";
        $body .= "Usuario: {$data['user_name']}\r\n";
        $body .= "Contraseña: {$data['user_pass']}\r\n";
        $body .= "\r\n";
        $body .= "Puede acceder al periódico a través del sisguiente enlace ".
                 "http://{$data['internal_name']}.{$domain}\r\n";
        $body .= "También puede acceder a la zona administrativa  a través del sisguiente enlace ".
                 "https://{$data['internal_name']}.{$domain}/admin/ usando el usuario ".
                 " y contraseña proporcionados en este correo.\r\n";
        $body .= "Le rogamos que cambie su contraseña en su primer acceso al area administrativa.\r\n";
        $body .= "\r\n";
        $body .= "En caso de que hubiera cualquier problema, no dude en ponerse ".
                 "en contacto a través de la dirección de correo {$companyMail}.\r\n";
        $body .= "\r\n";
        $body .= "Un saludo desde OpenHost SL.\r\n";
        $message->setBody($body);
        $message->setFrom($companyMail, "no-reply");

        $this->mailer->send($message);
    }

    public function sendMailToCompany($data, $companyMail, $domain)
    {
        $message = \Swift_Message::newInstance();
        $message->setTo(
            array($companyMail => $companyMail)
        );
        $message->setSubject("Se ha creado una nueva instancia de Onm");
        $body  = "Se ha creado un nueva periódico: {$data['name']}\r\n";
        $body .= "\r\n";
        $body .= "Los datos del nuevo usuario son los siguientes\r\n";
        $body .= "Usuario: {$data['user_name']}\r\n";
        $body .= "Email: {$data['user_mail']}\r\n";
        $body .= "Dirección del periódico: http://{$data['internal_name']}.{$domain}\r\n";
        $body .= "\r\n";
        $body .= "Un saludo desde OpenHost SL.\r\n";
        $message->setBody($body);
        $message->setFrom($companyMail, "no-reply");

        // Send the email
        $this->mailer->send($message);
    }

    /*
    * @url POST /instances/checkinstancename/
    */
    protected function postCheckInstanceName($url)
    {
        $im = Onm\Instance\InstanceManager::getInstance();
        return $im->checkInstanceExists($url);
    }

    /*
    * @url POST /instances/checkmailinuse/
    */
    protected function postCheckMailInUse($email)
    {
        $im = Onm\Instance\InstanceManager::getInstance();
        return $im->checkMailExists($email);
    }
}
