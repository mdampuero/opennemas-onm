<?php
/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Module\ModuleManager as mm;

/**
 * Handles all the request for Client page actions
 *
 * @package Backend_Controllers
 **/
class ClientInformationController extends Controller
{
    /**
     * Handles the default action
     *
     * @return void
     **/
    public function defaultAction()
    {
        $instance = $this->get('instance');

        // Get all modules grouped
        $availableModules = mm::getAvailableModulesGrouped();

        // Process activated modules with changes
        $hasChanges = false;
        $upgradeChanges = $downgradeChanges = [];

        if (is_array($instance->changes_in_modules)
            && !empty($instance->changes_in_modules)
        ) {
            $upgradeChanges = array_diff(
                $instance->changes_in_modules,
                $instance->activated_modules
            );
            $downgradeChanges = array_diff(
                $instance->changes_in_modules,
                $upgradeChanges
            );

            $hasChanges = true;

            $instance->activated_modules = array_diff(
                array_merge(
                    $instance->activated_modules,
                    $upgradeChanges
                ),
                $downgradeChanges
            );
        } else {
            $instance->changes_in_modules = [];
        }

        // Calculate total modules activated by plans
        $plans = [
            'Basic' => [
                'id'    => 'Base',
                'title' => _('Base'),
                'total' => 0
            ],
            'Profesional' => [
                'id'    => 'Profesional',
                'title' => _('Professional'),
                'total' => 0
            ],
            'Silver' => [
                'id'    => 'Silver',
                'title' => _('Advanced'),
                'total' => 0
            ],
            'Gold' => [
                'id'    => 'Gold',
                'title' => _('Expert'),
                'total' => 0
            ],
            'Other' => [
                'id'    => 'Other',
                'title' => _('Other'),
                'total' => 0
            ]
        ];

        foreach ($plans as &$plan) {
            foreach ($availableModules as $module) {
                if ($module['plan'] == $plan['id']) {
                    $plan['total']++;
                }
            }
        }

        // Get all modules array
        $modulesArray = mm::getAvailableModules();

        // Set support plan name and description
        $supportDescription = '';
        if (array_key_exists($instance->support_plan, $modulesArray)) {
            $supportDescription = mm::getModuleDescription(
                $instance->support_plan
            );
            $instance->support_plan = $modulesArray[$instance->support_plan];
        } else {
            $instance->support_plan = $modulesArray['SUPPORT_NONE'];
        }

        $maxUsers = $this->get('setting_repository')->get('max_users');

        $instance->activated_modules = array_values($instance->activated_modules);

        $billing = [];

        if (!empty($instance->metas)
            && array_key_exists('billing', $instance->metas)
        ) {
            $billing = $instace->metas['billing'];
        }

        return $this->render(
            'stats/stats_info.tpl',
            array(
                'instance'            => $instance,
                'billing'             => $billing,
                'upgrade'             => $upgradeChanges,
                'downgrade'           => $downgradeChanges,
                'available_modules'   => $availableModules,
                'plans'               => array_values($plans),
                'hasChanges'          => $hasChanges,
                'support_description' => $supportDescription,
                'max_users'           => ($maxUsers == 'NaN' || empty($maxUsers)) ? _('Unlimited') : $maxUsers,
            )
        );
    }

    /**
     * Send mail to upgrade instance plan/modules
     *
     * @param Request $request the request object
     *
     * @return void
     **/
    public function upgradeInstanceAction(Request $request)
    {
        // Fetch requested modules
        $modules = json_decode($request->request->get('modules'), true);
        $hasChanges = $request->request->filter(
            'hasChanges',
            null,
            FILTER_SANITIZE_STRING
        );

        if ($hasChanges) {
            $request->getSession()->getFlashBag()->add(
                'error',
                _(
                    'You had already sent an upgrade request. '.
                    'Your upgrade will be applied as soon as possible'
                )
            );

            return $this->redirect(
                $this->generateUrl('admin_client_info_page')
            );
        }

        // Fetch instance information
        $instance = $this->container->get('instance_manager')->current_instance;

        // Get available modules with name
        $availableModules = mm::getAvailableModules();
        $activatedModules = array_intersect(
            array_flip($availableModules),
            $instance->activated_modules
        );

        // Get hired/dismiss modules
        $modulesRequest = [
            'upgrade'   => array_diff($modules, $activatedModules),
            'downgrade' => array_diff($activatedModules, $modules)
        ];

        $modulesReqArray = array_merge(
            array_values($modulesRequest['upgrade']),
            array_values($modulesRequest['downgrade'])
        );

        // Update instance information with modules changes
        $instance->changes_in_modules = $modulesReqArray;

        // Set email subject and body
        $tplMail = new \TemplateAdmin(TEMPLATE_ADMIN);
        $tplMail->caching = 0;
        $mailSubject = sprintf(
            _('Upgrade instance request for %s'),
            $instance->name
        );
        $mailBody = $tplMail->fetch(
            'stats/emails/upgrade_instance.tpl',
            array('modules' => $modulesRequest)
        );

        // Build the message
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($mailSubject)
            ->setBody($mailBody, 'text/plain')
            ->setTo($this->container->getParameter('sales_email'))
            ->setFrom(array($instance->contact_mail => $instance->name));

        try {
            $mailer = $this->get('mailer');
            $mailer->send($message);

            // Save changes into database
            $this->get('instance_manager')->persist($instance);

            $this->view->assign(array('mailSent' => true));
        } catch (\Exception $e) {
            // Log this error
            $logger = getService('logger');
            $logger->notice(
                'Unable to send the upgrade instance request for '
                .'instance {$instance->name}: '.$e->getMessage()
            );

            $request->getSession()->getFlashBag()->add(
                'error',
                _('Unable to send your upgrade request. Please try it again.')
            );
        }

        $request->getSession()->getFlashBag()->add(
            'success',
            _('Your upgrade request has been sent.')
        );

        return $this->redirect($this->generateUrl('admin_client_info_page'));
    }
}
