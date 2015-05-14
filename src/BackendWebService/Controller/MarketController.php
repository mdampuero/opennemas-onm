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

        // Create email from template
        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Market purchase request')
            ->setFrom($instance->contact_mail)
            ->setTo($this->container->getParameter('sales_email'))
            ->setBody(
                $this->renderView(
                    'market/email/_purchase.tpl',
                    [
                        'instance' => $instance,
                        'modules'  => $purchased
                    ]
                ),
                'text/html'
            );

        // Send an email
        $this->get('mailer')->send($message);

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

        return new JsonResponse(
            [ 'results' => $modules, 'activated' => $activated ]
        );
    }
}
