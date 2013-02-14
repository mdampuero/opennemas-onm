<?php

/**
 * This file is part of the onm package.
 * (c) 2009-2013 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
/**
 *
 *
 * @package    Onm
 * @subpackage Rest\Manager
 * @author     me
 **/
namespace Onm\Rest\Manager;

class Instances extends \Onm\Rest\RestBase
{
    public $restler;
    private $mailer;

    /**
     * @url POST create
     * @access protected
     *
     * @param string $site_name {@from body}
     * @param string $internal_name {@from body}
     * @param string $domains {@from body}
     * @param string $user_name {@from body}
     * @param string $password {@from body}
     * @param string $contact_mail {@type email} {@from body}
     * @param string $contact_IP {@from body}
     * @param string $timezone {@from body}
     * @param string $token {@from body}
     * @param string $language {@from body}
     *
     * @return mixed
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
        $language
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

        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone("UTC"));

        //Get all the Post data
        $data = array(
            'contact_IP'    => filter_var($contact_IP, FILTER_SANITIZE_STRING),
            'name'          => filter_var($site_name, FILTER_SANITIZE_STRING),
            'user_name'     => filter_var($user_name, FILTER_SANITIZE_STRING),
            'user_mail'     => filter_var($contact_mail, FILTER_SANITIZE_EMAIL),
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
        $im = \Onm\Instance\InstanceManager::getInstance();
        // Check for reapeted internalnameshort and if so, add a number at the end
        $data = $im->checkInternalShortName($data);
        $errors = $im->create($data);

        if (is_array($errors) ) {
            return $errors;
        }

        $companyMail = $this->restler->wsParams["company_mail"];
        $domain = $this->restler->container->getParameter("base_domain");
        $this->sendMails($data, $companyMail, $domain, $language);

        return true;
    }

    private function sendMails($data, $companyMail, $domain, $language)
    {
        require_once SITE_VENDOR_PATH.'/swiftmailer/swiftmailer/lib/swift_required.php';

        // $transport = \Swift_MailTransport::newInstance();
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, "tls")
            ->setUsername('toni@openhost.es')
            ->setPassword('_b4D3l2F1_');
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
        $message->setSubject("{$data['name']} "._("is now on-line"));//ya está online

        $this->restler->view->assign(
            array(
                'data'        => $data,
                'companyMail' => $companyMail,
                'domain'      => $domain,
            )
        );
        $body =  $this->restler->view->fetch('instances/mails/newInstanceToUser.tpl');

        $message->setBody($body);
        $message->setFrom($companyMail, "no-reply");

        $this->mailer->send($message);
    }

    private function sendMailToCompany($data, $companyMail, $domain)
    {
        $message = \Swift_Message::newInstance();
        $message->setTo(
            array($companyMail => $companyMail)
        );
        $message->setSubject(_("A new opennemas instance has been created"));
        //Se ha creado una nueva instancia de Onm

        $this->restler->view->assign(
            array(
                'data'        => $data,
                'companyMail' => $companyMail,
                'domain'      => $domain,
            )
        );
        $body =  $this->restler->view->fetch('instances/mails/newInstanceToCompany.tpl');

        $message->setBody($body);
        $message->setFrom($companyMail, "no-reply");

        // Send the email
        $this->mailer->send($message);
    }

    /**
     * @url POST checkinstancename
     *
     * @param string $url {@from body}
     *
     * @return boolean
     */
    protected function postCheckInstanceName($url)
    {
        $im = \Onm\Instance\InstanceManager::getInstance();
        $url = filter_var($url, FILTER_SANITIZE_STRING);
        return $im->checkInstanceExists($url);
    }

    /**
     * @url POST checkmailinuse
     *
     * @param string $email {@type email} {@from body}
     *
     * @return boolean
     */
    public function postCheckMailInUse($email)
    {
        $im = \Onm\Instance\InstanceManager::getInstance();
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return $im->checkMailExists($email);
    }
}
