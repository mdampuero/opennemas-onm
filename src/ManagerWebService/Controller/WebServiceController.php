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
use Symfony\Component\HttpFoundation\JsonResponse;

use Onm\Instance\Instance;
use Onm\Instance\InstanceCreator;
use Onm\Exception\AssetsNotCopiedException;
use Onm\Exception\InstanceNotConfiguredException;
use Onm\Exception\DatabaseNotRestoredException;

use Onm\Framework\Controller\Controller;

/**
 * Handles the actions for the manager web service
 *
 * @package ManagerWebService_Controller
 **/
class WebServiceController extends Controller
{
    /**
     * Creates a new instance.
     *
     * @param  Request      $request The request object.
     * @return JsonResponse          The response object.
     */
    public function createAction(Request $request)
    {
        $instanceCreator = $this->container->getParameter("instance_creator");

        if (is_object($this->checkAuth($request))) {
            return new JsonResponse('Auth not valid', 403);
        }

        $instance = new Instance();

        $instance->internal_name = $request->request->filter('subdomain', '', FILTER_SANITIZE_STRING);
        $instance->name          = $request->request->filter('instance_name', '', FILTER_SANITIZE_STRING);
        $instance->contact_mail  = $request->request->filter('user_email', '', FILTER_SANITIZE_STRING);

        $errors = $this->validateInstanceData(array(
            'subdomain'     => $instance->internal_name,
            'instance_name' => $instance->name,
            'user_email'    => $instance->contact_mail,
        ));

        if (count($errors) > 0) {
            return new JsonResponse(array('success' => false, 'errors' => $errors), 400);
        }

        $instance->domains       = array($instance->internal_name . '.' . $instanceCreator['base_domain']);
        $instance->main_domain   = 1;
        $instance->activated     = 1;
        $instance->plan          = $request->request->filter('plan', 'basic', FILTER_SANITIZE_STRING);
        $instance->price         = 0;

        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone("UTC"));
        $instance->created = $date->format('Y-m-d H:i:s');

        $instance->settings = array(
            'TEMPLATE_USER' => $instanceCreator['template'],
            'MEDIA_URL'     => "",
        );

        // Also get timezone if comes from openhost form
        $timezone = $request->request->filter('timezone', '', FILTER_SANITIZE_STRING);
        if (!empty ($timezone)) {
            $allTimezones = \DateTimeZone::listIdentifiers();
            foreach ($allTimezones as $key => $value) {
                if ($timezone == $value) {
                    $timezone = $key;
                }
            }
        }

        $instance->external = array(
            'contact_IP'    => $request->request->filter('contact_IP', '', FILTER_SANITIZE_STRING),
            'contact_mail'  => $instance->contact_mail,
            'contact_name'  => $instance->contact_mail,
            'site_language' => $request->request->filter('language', '', FILTER_SANITIZE_STRING),
            'time_zone'     => $timezone,
            'site_created'  => $date->format('Y-m-d H:i:s'),
            'name'          => $instance->name,
            'internal_name' => $instance->internal_name,
        );

        $user = array(
            'username' => $instance->contact_mail,
            'email'    => $instance->contact_mail,
            'password' => $request->request->filter('user_password', '', FILTER_SANITIZE_STRING),
            'token'    => md5(uniqid(mt_rand(), true))
        );

        $errors = array();

        $im = $this->get('instance_manager');
        $creator = new InstanceCreator($im->getConnection());

        // Check for repeated internalnameshort and if so, add a number at the end
        $im->checkInternalName($instance);

        try {
            $im->persist($instance);


            $creator->createDatabase($instance->id);
            $creator->copyDefaultAssets($instance->internal_name);

            $im->configureInstance($instance);
            $im->createUser($instance->id, $user);
        } catch (DatabaseNotRestoredException $e) {
            $errors[] = $e->getMessage();

            $creator->deleteDatabase($instance->id);
            $im->remove($instance);

        } catch (AssetsNotCopiedException $e) {
        } catch (InstanceNotConfigured $e) {
            // Assets folder in use (wrong deletion) or permissions issue
            $errors[] = $e->getMessage();

            $creator->deleteAssets($instance->internal_name);
            $creator->deleteDatabase($instance->id);
            $im->remove($instance);
        }

        if (is_array($errors) && count($errors) > 0) {
            return new JsonResponse(array('success' => false, 'errors' => $errors), 400);
        }

        $companyMail = array(
            'company_mail' => $this->params["company_mail"],
            'info_mail'    => $this->params["info_mail"],
            'sender_mail'  => $this->params["no_reply_sender"],
            'from_mail'    => $this->params["no_reply_from"],
        );

        $data = array(
            'name'          => $instance->name,
            'internal_name' => $instance->internal_name,
            'user_mail'     => $instance->contact_mail,
            'user_name'     => $instance->contact_mail,
        );

        $language = $instance->external['site_language'];
        $plan = $instance->plan;

        $domain = $instanceCreator['base_domain'];
        $this->sendMails($data, $companyMail, $domain, $language, $plan);

        return new JsonResponse(
            array(
                'success'      => true,
                'instance_url' => $instance->domains[0],
                'enable_url'   => $instance->domains[0]
                    . '/admin/login?token=' . $user['token']
            ),
            200
        );
    }

    /**
     * Checks if it is an authorized request.
     *
     * @param  Request $request The request object.
     * @return boolean          True if the request is authorized. Otherwise,
     *                          returns false.
     */
    private function checkAuth(Request $request)
    {
        $this->params = $this->container
            ->getParameter("manager_webservice");

        $signature = hash_hmac(
            'sha1',
            $request->request->get('timestamp'),
            $this->params["api_key"]
        );

        if ($signature === $request->request->get('signature', null)) {
            return true;
        }

        return false;
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

    private function sendMails($data, $companyMail, $domain, $language, $plan)
    {
        $this->sendMailToUser($data, $companyMail, $domain);
        $this->sendMailToCompany($data, $companyMail, $domain, $plan);
    }

    /**
     * Execute all validate methods defined in validatorComponent class
     * These method are detected because their name begins with validate
     * @return boolean
     */
    private function validateInstanceData($data)
    {
        $validator = new \Onm\Instance\Validator($data, $this->get('instance_manager'));

        return $validator->validate();
    }
}
