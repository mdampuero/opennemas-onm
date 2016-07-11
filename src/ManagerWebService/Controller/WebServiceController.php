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
use Onm\Framework\Controller\Controller;
use Onm\Exception\InstanceNotConfiguredException;
use Onm\Exception\DatabaseNotRestoredException;

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

        $companyMail = array(
            'company_mail' => $this->params["company_mail"],
            'info_mail'    => $this->params["info_mail"],
            'sender_mail'  => $this->params["no_reply_sender"],
            'from_mail'    => $this->params["no_reply_from"],
        );

        $instance = new Instance();

        $instance->internal_name = $request->request->filter('subdomain', '', FILTER_SANITIZE_STRING);
        $instance->name          = $request->request->filter('instance_name', '', FILTER_SANITIZE_STRING);
        $instance->contact_mail  = $request->request->filter('user_email', '', FILTER_SANITIZE_STRING);

        $errors = $this->validateInstanceData(array(
            'subdomain'     => $instance->internal_name,
            'instance_name' => $instance->name,
            'user_email'    => $instance->contact_mail,
        ));

        $im = $this->get('instance_manager');

        // Check for repeated internalnameshort and if so, add a number at the end
        $iv = $this->get('onm.validator.instance');
        $iv->validateInternalName($instance);

        if (count($errors) > 0) {
            return new JsonResponse(array('success' => false, 'errors' => $errors), 400);
        }

        $instance->domains           = array($instance->internal_name . '.' . $instanceCreator['base_domain']);
        $instance->main_domain       = 1;
        $instance->activated         = 1;
        $instance->plan              = $request->request->filter('plan', 'basic', FILTER_SANITIZE_STRING);
        $instance->price             = 0;
        $instance->activated_modules = [
            'ADVANCED_SEARCH',
            'ARTICLE_MANAGER',
            'CATEGORY_MANAGER',
            'COMMENT_MANAGER',
            'FILE_MANAGER',
            'FRONTPAGE_MANAGER',
            'IMAGE_MANAGER',
            'KEYWORD_MANAGER',
            'LIBRARY_MANAGER',
            'MENU_MANAGER',
            'OPINION_MANAGER',
            'SETTINGS_MANAGER',
            'STATIC_PAGES_MANAGER',
            'TRASH_MANAGER',
            'USERVOICE_SUPPORT',
            'WIDGET_MANAGER',

            // Themes included
            'es.openhost.theme.basic',
        ];

        $instance->metas = [ 'purchased' => [ 'es.openhost.theme.basic' ] ];

        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone("UTC"));
        $instance->created = $date->format('Y-m-d H:i:s');

        $instance->settings = array(
            'TEMPLATE_USER' => $instanceCreator['template'],
            'MEDIA_URL'     => "",
        );

        // Also get timezone if comes from openhost form
        $timezone = $request->request->filter('timezone', '', FILTER_SANITIZE_STRING);
        if (!empty($timezone)) {
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

        try {
            $creator = new InstanceCreator($im->getConnection());
            $im->persist($instance);

            $creator->createDatabase($instance->id);
            $creator->copyDefaultAssets($instance->internal_name);

            $im->configureInstance($instance);
            $im->createUser($instance->id, $user);
        } catch (DatabaseNotRestoredException $e) {
            // Can not create database
            $errors[] = $e->getMessage();

            $creator->deleteDatabase($instance->id);
            $im->remove($instance);

            $this->sendErrorMail($companyMail, $instance, $e);
        } catch (IOException $e) {
            // Can not copy default assets
            $errors[] = $e->getMessage();

            $creator->deleteDatabase($instance->id);

            $this->sendErrorMail($companyMail, $instance, $e);
        } catch (\Exception $e) {
            // Can not save settings in instance database
            $errors[] = $e->getMessage();

            $creator->deleteAssets($instance->internal_name);
            $creator->deleteDatabase($instance->id);
            $im->remove($instance);

            $this->sendErrorMail($companyMail, $instance, $e);
        }

        try {
            $data = [
                'name'          => $instance->name,
                'internal_name' => $instance->internal_name,
                'user_mail'     => $instance->contact_mail,
                'user_name'     => $instance->contact_mail,
            ];

            $language = $instance->external['site_language'];
            $plan     = $instance->plan;

            $domain = $instanceCreator['base_domain'];
            $this->sendMails($data, $companyMail, $domain, $language, $plan);
        } catch (\Exception $e) {
            $errors['all'] = ['Unable to send emails'];
            error_log($e->getMessage());
        }

        if (is_array($errors) && count($errors) > 0) {
            return new JsonResponse(['success' => false, 'errors' => $errors], 400);
        }

        return new JsonResponse(
            [
                'success'      => true,
                'instance_url' => $instance->domains[0],
                'enable_url'   => $instance->domains[0]
                . '/admin/login?token=' . $user['token']
            ],
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

    private function sendErrorMail($emails, $instance, $exception)
    {
        // Prepare message
        $message = \Swift_Message::newInstance();
        $message->setFrom($emails['from_mail'])
            ->setTo($emails['company_mail'])
            ->setSender($emails['sender_mail'], "Opennemas")
            ->setSubject(_("Error when creating a new instance"))
            ->setBody(
                $this->renderView(
                    'instances/mails/instanceCreationError.tpl',
                    array(
                        'instance'  => $instance,
                        'exception' => $exception
                    )
                )
            );

        // Send message
        $this->get('mailer')->send($message);
    }

    private function sendMailToCompany($data, $companyMail, $domain, $plan)
    {
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
        $this->get('mailer')->send($message);
        $this->get('logger')->notice("Sending mail to company {$companyMail['info_mail']} - new instance - {$data['name']}");
    }

    private function sendMailToUser($data, $companyMail, $domain)
    {
        $instanceBaseURL = "http://".$data['internal_name'].".".$domain;

        // Prepare message
        $message = \Swift_Message::newInstance();
        $message->setFrom([$companyMail['from_mail'] => 'Opennemas'])
            ->setTo([$data['user_mail'] => $data['user_name']])
            ->setSender($companyMail['sender_mail'], "Opennemas")
            ->setSubject(sprintf(_("Your newspaper is now live"), $data['name']))
            ->setBody(
                $this->renderView(
                    'instances/mails/newInstanceToUser.tpl',
                    array(
                        'data'              => $data,
                        'domain'            => $domain,
                        'companyMail'       => $companyMail['company_mail'],
                        'instance_base_url' => $instanceBaseURL,
                    )
                )
            );

        // Send message
        $this->get('mailer')->send($message);
        $this->get('logger')->notice("Sending mail to user - new instance - {$data['name']}");
    }

    private function sendMails($data, $companyMail, $domain, $language, $plan)
    {
        $this->sendMailToUser($data, $companyMail, $domain);
        $this->sendMailToCompany($data, $companyMail, $domain, $plan);
        // Unused var $language
        unset($language);
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
