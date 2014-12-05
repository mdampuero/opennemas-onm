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
use Onm\Instance\InstanceManager as im;
use Onm\Module\ModuleManager as mm;
use Onm\Settings as s;

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
        // Fetch instance information
        $instance = $this->container->get('instance_manager')->current_instance;
        // Get all modules
        $availableModules = mm::getAvailableModulesGrouped();

        // Process activated modules with changes
        $hasChanges = false;
        if (is_array($instance->changes_in_modules)
            && !empty($instance->changes_in_modules)
        ) {
            $hasChanges = (
                count($instance->changes_in_modules['upgrade']) > 0 ||
                count($instance->changes_in_modules['downgrade']) > 0
            );
            $instance->activated_modules = array_diff(
                array_merge(
                    $instance->activated_modules,
                    array_values($instance->changes_in_modules['upgrade'])
                ),
                array_values($instance->changes_in_modules['downgrade'])
            );
        } else {
            $instance->changes_in_modules = array();
        }

        // Calculate total modules activated by plans
        $plans = [
            'Profesional' => 0,
            'Silver'      => 0,
            'Gold'        => 0,
            'Other'       => 0
        ];

        foreach ($plans as $plan => &$total) {
            foreach ($availableModules as $module) {
                if ($module['plan'] == $plan &&
                    in_array($module['id'], $instance->activated_modules)
                ) {
                    $total++;
                }
            }
        }

        // Set support plan name
        if ($instance->support_plan) {
            $instance->support_plan = mm::getAvailableModules()[$instance->support_plan];
        }

        return $this->render(
            'stats/stats_info.tpl',
            array(
                'instance' => $instance,
                'available_modules' => $availableModules,
                'plans' => $plans,
                'has_changes' => $hasChanges,
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
        $modules = $request->request->get('modules');
        $waitingUpdate = $request->request->filter('waiting-upgrade', null, FILTER_SANITIZE_STRING);

        if ($waitingUpdate) {
            $request->getSession()->getFlashBag()->add(
                'error',
                _(
                    'You had already sent a upgrade request. '.
                    'Your upgrade will be applied as soon as possible'
                )
            );

            return $this->redirect($this->generateUrl('admin_client_info_page'));
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
            'upgrade' => array_diff($modules, $activatedModules),
            'downgrade' => array_diff($activatedModules, $modules)
        ];

        // Update instance information with modules changes
        $instance->changes_in_modules = serialize($modulesRequest);

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
