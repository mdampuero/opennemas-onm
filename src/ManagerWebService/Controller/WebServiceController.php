<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace ManagerWebService\Controller;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Handles the actions for the manager web service
 *
 * @package ManagerWebService_Controller
 **/
class WebServiceController extends Controller
{
    /**
     *
     *
     * @return mixed
    */
    public function createAction(Request $request)
    {
        $authResult = $this->checkAuth($request);
        if (is_object($authResult)) {
            return $authResult;
        }

        $internalName = $request->request->filter('subdomain', '', FILTER_SANITIZE_STRING);
        $siteName     = $request->request->filter('instance_name', '', FILTER_SANITIZE_STRING);
        $userName     = $request->request->filter('user_email', '', FILTER_SANITIZE_STRING);
        $password     = substr(hash_hmac('sha512', rand(), $this->webserviceParameters["api_key"]), 0, 12);
        $userPass     = $request->request->filter('user_password', '', FILTER_SANITIZE_STRING);
        $contactMail  = $request->request->filter('user_email', '', FILTER_SANITIZE_STRING);
        $contactIP    = $request->request->filter('contact_IP', '', FILTER_SANITIZE_STRING);
        $timezone     = $request->request->filter('timezone', '', FILTER_SANITIZE_STRING);
        $token        = md5(uniqid(mt_rand(), true));
        $language     = $request->request->filter('language', '', FILTER_SANITIZE_STRING);
        $plan         = $request->request->filter('plan', '', FILTER_SANITIZE_STRING);

        $errors = $this->validateInstanceData(array(
            'subdomain'     => $internalName,
            'instance_name' => $siteName,
            'user_email'    => $contactMail,
        ));
        if (count($errors) > 0) {
            return new JsonResponse(array('success' => false, 'errors' => $errors), 400);
        }

        //Force internal_name lowercase
        //If is creating a new instance, get DB params on the fly
        $internalNameShort = strtolower(trim(substr($internalName, 0, 11)));

        $instanceCreator = $this->container->getParameter("instance_creator");
        $settings = array(
            'TEMPLATE_USER' => $instanceCreator['template'],
            'MEDIA_URL'     => "",
            'BD_TYPE'       => "mysqli",
            'BD_HOST'       => "localhost",
            'BD_USER'       => $internalNameShort,
            'BD_PASS'       => \Onm\StringUtils::generatePassword(16),
            'BD_DATABASE'   => $internalNameShort,
            'TOKEN'         => $token,
        );

        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone("UTC"));

        //Get all the Post data
        $data = array(
            'contact_IP'    => $contactIP,
            'name'          => $siteName,
            'user_name'     => $userName,
            'user_mail'     => $contactMail,
            'user_password' => $userPass,
            'token'         => $token,
            'internal_name' => $internalName,
            'domains'       => $internalName.'.'.$instanceCreator['base_domain'],
            'activated'     => 1,
            'settings'      => $settings,
            'site_created'  => $date->format('Y-m-d H:i:s'),
            'owner_fk_user' => 0,
            'price'         => 0,
            'plan'          => $plan,
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
        $im = $this->get('instance_manager');
        // Check for repeated internalnameshort and if so, add a number at the end
        $data = $im->checkInternalShortName($data);
        $errors = $im->create($data);
        if (is_array($errors) && count($errors) > 0) {
            return new JsonResponse(array('success' => false, 'errors' => $errors), 400);
        }

        $companyMail = array(
            'company_mail' => $this->webserviceParameters["company_mail"],
            'info_mail'    => $this->webserviceParameters["info_mail"],
            'sender_mail'  => $this->webserviceParameters["no_reply_sender"],
            'from_mail'    => $this->webserviceParameters["no_reply_from"],
        );

        $domain = $instanceCreator['base_domain'];
        $this->sendMails($data, $companyMail, $domain, $language, $plan);

        return new JsonResponse(
            array(
                'success'      => true,
                'instance_url' => $data['domains'],
                'enable_url'   => $data['domains']
                    . '/admin/login?token=' . $token
            ),
            200
        );
    }

    private function sendMails($data, $companyMail, $domain, $language, $plan)
    {
        $this->sendMailToUser($data, $companyMail, $domain);
        $this->sendMailToCompany($data, $companyMail, $domain, $plan);
    }

    private function sendMailToUser($data, $companyMail, $domain)
    {
        $this->view = new \TemplateManager();

        // Prepare message
        $message = \Swift_Message::newInstance();
        $message->setFrom($companyMail['from_mail'])
                ->setTo(array($data['user_mail'] => $data['user_name']))
                ->setSender($companyMail['sender_mail'], "Opennemas")
                ->setSubject("{$data['name']} "._("is now on-line"))
                ->setBody(
                    $this->renderView(
                        'instances/mails/newInstanceToUser.tpl',
                        array(
                            'data'        => $data,
                            'companyMail' => $companyMail['company_mail'],
                            'domain'      => $domain,
                        )
                    )
                );

        // Send message
        $mailer = $this->get('mailer');
        $mailer->send($message);

        $this->get('logger')->notice("Sending mail to user - new instance - {$data['name']}");
    }

    private function sendMailToCompany($data, $companyMail, $domain, $plan)
    {
        $this->view = new \TemplateManager();

        // Prepare message
        $message = \Swift_Message::newInstance();
        $message->setFrom($companyMail['from_mail'])
                ->setTo(array($companyMail['info_mail'] => $companyMail['info_mail']))
                ->setSender($companyMail['sender_mail'], "Opennemas")
                ->setSubject(_("A new opennemas instance has been created"))
                ->setBody(
                    $this->renderView(
                        'instances/mails/newInstanceToCompany.tpl',
                        array(
                            'data'        => $data,
                            'domain'      => $domain,
                            'plan'        => $plan
                        )
                    )
                );

        // Send message
        $sent = $this->get('mailer')->send($message, $failures);
        $this->get('logger')->notice("Sending mail to company {$companyMail['info_mail']}- new instance - {$data['name']}");
    }

    /**
     * Execute all validate methods defined in validatorComponent class
     * These method are detected because their name begins with validate
     * @return boolean
     */
    private function validateInstanceData($data)
    {
        $validator = new \Onm\Instance\Validator($data, $this->get('instance_manager'));

        $correct = $validator->validate();

        return $correct;
    }

    /**
     * Checks if the request was done through encrypted HTTP
     *
     * @return boolean true if the request was done with HTTPS
     **/
    private function isHttps()
    {
        if (!empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS']!="off"
        ) {
            return true;
        }
        return false;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    private function checkAuth($request)
    {
        $this->webserviceParameters = $this->container->getParameter("manager_webservice");

        // return true;
        // if (!$this->isHttps()) {
        //     throw new \Exception;
        // }

        $signature = hash_hmac(
            'sha1',
            $request->request->get('timestamp'),
            $this->webserviceParameters["api_key"]
        );

        $signatureRequest = $request->request->get('signature', null);
        if ($signature === $signatureRequest
        ) {
            return true;
        }

        return new JsonResponse('Auth not valid', 403);
    }
}
