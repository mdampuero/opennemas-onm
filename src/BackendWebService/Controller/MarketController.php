<?php

namespace BackendWebService\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MarketController extends Controller
{
    /**
     * Request a modules purchase to the sales department.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function checkoutAction(Request $request)
    {
        if (!$request->request->get('modules')) {
            return new JsonResponse(
                _('Your request could not been registered'),
                400
            );
        }

        $available = \Onm\Module\ModuleManager::getAvailableModules();
        $instance  = $this->get('instance');
        $modules   = $request->request->get('modules');

        // Filter request to ignore invalid modules
        $modules = array_filter($modules, function ($e) use ($available) {
            return array_key_exists($e, $available);
        });

        // Get names for filtered modules to use in template
        $purchased = array_intersect_key($available, array_flip($modules));

        $this->sendEmailToSales($instance, $purchased);
        $this->sendEmailToCustomer($instance, $purchased);

        return new JsonResponse(_('Your request has been registered'));
    }

    /**
     * Returns the list of modules and current activated modules.
     *
     * @return JsonResponse The response object.
     */
    public function listAction()
    {
        $modules   = \Onm\Module\ModuleManager::getAvailableModulesGrouped();
        $activated = $this->get('instance')->activated_modules;

        // Remove internal modules
        $modules = array_filter($modules, function ($a) {
            if (array_key_exists('type', $a) && $a['type'] === 'internal') {
                return false;
            }

            return true;
        });

        // Non-purchased modules first
        usort($modules, function ($a, $b) use ($activated) {
            if (in_array($a['id'], $activated)
                && in_array($b['id'], $activated)
            ) {
                return 0;
            }

            if (in_array($a['id'], $activated)) {
                return 1;
            }

            return -1;
        });

        return new JsonResponse(
            [ 'results' => $modules, 'activated' => $activated ]
        );
    }

    /**
     * Sends an email to the customer.
     *
     * @param Instance $instance The instance to upgrade.
     * @param array    $modules  The requested modules.
     */
    private function sendEmailToCustomer($instance, $modules)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Market purchase request')
            ->setFrom($this->container->getParameter('sales_email'))
            ->setTo($instance->contact_mail)
            ->setBody(
                $this->renderView(
                    'market/email/_purchaseToCustomer.tpl',
                    [
                        'instance' => $instance,
                        'modules'  => $modules
                    ]
                ),
                'text/html'
            );

        $this->get('mailer')->send($message);
    }

    /**
     * Sends an email to sales department.
     *
     * @param Instance $instance The instance to upgrade.
     * @param array    $modules  The requested modules.
     */
    private function sendEmailToSales($instance, $modules)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Market purchase request')
            ->setFrom($instance->contact_mail)
            ->setTo($this->container->getParameter('sales_email'))
            ->setBody(
                $this->renderView(
                    'market/email/_purchaseToSales.tpl',
                    [
                        'instance' => $instance,
                        'modules'  => $modules,
                        'user'     => $this->getUser()
                    ]
                ),
                'text/html'
            );

        $this->get('mailer')->send($message);
    }
}
