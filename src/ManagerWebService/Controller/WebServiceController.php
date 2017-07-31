<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Common\ORM\Entity\Instance;
use Common\ORM\Entity\User;
use Onm\Instance\InstanceCreator;
use Common\Core\Controller\Controller;
use Onm\Exception\InstanceNotConfiguredException;
use Onm\Exception\DatabaseNotRestoredException;

/**
 * Handles the actions for the manager web service
 *
 * @package ManagerWebService_Controller
 */
class WebServiceController extends Controller
{
    /**
     * Checks if it is an authorized request.
     *
     * @param Request $request The request object.
     *
     * @return boolean True if the request is authorized. False, otherwise.
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

    /**
     * Creates a new instance.
     *
     * @param  Request      $request The request object.
     * @return JsonResponse          The response object.
     */
    public function createAction(Request $request)
    {
        if (is_object($this->checkAuth($request))) {
            return new JsonResponse('Auth not valid', 403);
        }

        $params    = $this->container->getParameter("instance_creator");
        $subdomain = $request->request->filter('subdomain', '', FILTER_SANITIZE_STRING);
        $pack      = $this->get('orm.manager')->getRepository('Extension')
            ->findOneBy('uuid = "BASIC_PACK"');

        $instance = new Instance([
            'internal_name'     => mb_strtolower($subdomain),
            'name'              => $request->request->filter('instance_name', '', FILTER_SANITIZE_STRING),
            'contact_mail'      => $request->request->filter('user_email', '', FILTER_SANITIZE_STRING),
            'domains'           => [ mb_strtolower($subdomain) . '.' . $params['base_domain'] ],
            'main_domain'       => 1,
            'activated'         => true,
            'plan'              => $request->request->filter('plan', 'basic', FILTER_SANITIZE_STRING),
            'price'             => 0,
            'activated_modules' => [ 'es.openhost.theme.basic' ],
            'purchased'         => [ 'es.openhost.theme.basic' ],
            'country'           => $request->request->filter('country', 'ES', FILTER_SANITIZE_STRING),
            'created'           => new \DateTime(),
            'settings'          => [ 'TEMPLATE_USER' => $params['template'] ],
            'support_plan'      => 'SUPPORT_NONE'
        ]);


        $instance->activated_modules =
            array_merge($instance->activated_modules, $pack->modules_included);

        $validator = $this->get('core.instance.validator');
        $validator->validate($instance);

        if ($validator->hasErrors()) {
            error_log('Instance data validation not passed: ' . json_encode($validator->getErrors()));

            return new JsonResponse([
                'success' => false,
                'errors' => $validator->getErrors()
            ], 400);
        }

        // Also get timezone if comes from openhost form
        $timezone = $request->request->filter('timezone', '', FILTER_SANITIZE_STRING);
        if (empty($timezone)
            || !in_array($timezone, \DateTimeZone::listIdentifiers())
        ) {
            $timezone = 'UTC';
        }

        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('User');
        $creator   = new InstanceCreator(
            $em->getConnection('instance'),
            $this->get('application.log')
        );

        $settings = [
            'site_language' => $request->request->filter('language', '', FILTER_SANITIZE_STRING),
            'time_zone'     => $timezone,
            'site_created'  => $instance->created,
        ];

        $user = new User($converter->objectify([
            'activated'     => true,
            'email'         => $instance->contact_mail,
            'fk_user_group' => [ 5 ],
            'name'          => $instance->contact_mail,
            'token'         => md5(uniqid(mt_rand(), true)),
            'type'          => 0,
            'username'      => $instance->contact_mail
        ]));

        $user->password = $this->get('core.security.encoder.password')
            ->encodePassword(
                $request->request->filter('user_password', '', FILTER_SANITIZE_STRING),
                null
            );

        try {
            $errors = [];
            $companyMail = array(
                'company_mail' => $this->params["company_mail"],
                'info_mail'    => $this->params["info_mail"],
                'sender_mail'  => $this->params["no_reply_sender"],
                'from_mail'    => $this->params["no_reply_from"],
            );

            $em->persist($instance);
            $instance->refresh();
            $instance->settings['BD_DATABASE'] = $instance->id;
            $em->persist($instance);

            // Create instance database and media files
            $creator->createDatabase($instance->id);
            $creator->copyDefaultAssets($instance->internal_name);

            $em->getConnection('instance')
                ->selectDatabase($instance->getDatabaseName());

            // Save settings and user
            $em->getDataSet('Settings', 'instance')->set($settings);
            $em->persist($user, 'instance');
        } catch (DatabaseNotRestoredException $e) {
            // Can not create database
            $errors[] = $e->getMessage();

            $creator->deleteDatabase($instance->id);
            $em->remove($instance);
            $this->reportInstanceCreationError($companyMail, $instance, $e);
        } catch (IOException $e) {
            // Can not copy default assets
            $errors[] = $e->getMessage();

            $creator->deleteDatabase($instance->id);
            $this->reportInstanceCreationError($companyMail, $instance, $e);
        } catch (\Exception $e) {
            // Can not save settings in instance database
            $errors[] = $e->getMessage();

            $creator->deleteAssets($instance->internal_name);
            $creator->deleteDatabase($instance->id);
            $em->remove($instance);
            $this->reportInstanceCreationError($companyMail, $instance, $e);
        }

        try {
            if (count($errors) <= 0) {
                $this->sendMails(
                    [
                        'name'          => $instance->name,
                        'internal_name' => $instance->internal_name,
                        'user_mail'     => $instance->contact_mail,
                        'user_name'     => $instance->contact_mail,
                    ],
                    $companyMail,
                    $params['base_domain'],
                    $instance->external['site_language'],
                    $instance->plan
                );
            }
        } catch (\Exception $e) {
            $errors['all'] = ['Unable to send emails'];
            error_log('Error while sending instance creation emails: ' . $e->getMessage());
        }

        if (is_array($errors) && count($errors) > 0) {
            return new JsonResponse(['success' => false, 'errors' => $errors], 400);
        }

        return new JsonResponse([
            'success'      => true,
            'instance_url' => $instance->domains[0],
            'enable_url'   => $instance->domains[0] . '/admin/login?token='
                . $user->token
        ]);
    }

    private function sendMails($data, $companyMail, $domain, $plan)
    {
        $this->sendMailToUser($data, $companyMail, $domain);
        $this->sendMailToCompany($data, $companyMail, $domain, $plan);
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
                    'instance/emails/newInstanceToCompany.tpl',
                    array(
                        'data'        => $data,
                        'domain'      => $domain,
                        'plan'        => $plan
                    )
                )
            );

        // Send message
        $this->get('mailer')->send($message);
        $this->get('application.log')->notice("Sending mail to company {$companyMail['info_mail']} - new instance - {$data['name']}");
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
                    'instance/emails/newInstanceToUser.tpl',
                    array(
                        'data'              => $data,
                        'domain'            => $domain,
                        'companyMail'       => $companyMail['company_mail'],
                        'instance_base_url' => $instanceBaseURL,
                    )
                ),
                'text/html'
            );

        // Send message
        $this->get('mailer')->send($message);
        $this->get('application.log')->notice("Sending mail to user - new instance - {$data['name']}");
    }

    private function reportInstanceCreationError($emails, $instance, $exception)
    {
        // Prepare message
        $message = \Swift_Message::newInstance();
        $message->setFrom($emails['from_mail'])
            ->setTo($emails['company_mail'])
            ->setSender($emails['sender_mail'], "Opennemas")
            ->setSubject(_("Error when creating a new instance"))
            ->setBody(
                $this->renderView(
                    'instance/emails/instanceCreationError.tpl',
                    array(
                        'instance'  => $instance,
                        'exception' => $exception
                    )
                ),
                'text/html'
            );

        // Send message
        $this->get('mailer')->send($message);
        error_log("Error while creating instance. ".$exception->getMessage()
            .'. Instance Data: '.json_encode($instance));
    }
}
