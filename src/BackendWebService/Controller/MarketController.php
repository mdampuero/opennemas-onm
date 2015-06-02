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

        $packs = [
            [
                'id'               => 'BASIC',
                'name'             => _('Basic pack'),
                'description'      => _('Features the basic functionality for your newspaper for free.'),
                'long_description' => _('<p>Publishing your news is <strong>FREE!</strong></p>
                    This pack includes:
                    <ul>
                        <li>Frontpage manager</li>
                        <li>Widget manager</li>
                        <li>Opinion articles manager</li>
                        <li>Comments manager</li>
                        <li>Images and files uploading</li>
                        <li>Utilities: Trash, Search Advanced...</li>
                        <li>Support via tickets</li>
                        <li>Media storage: 500MB</li>
                        <li>Page views: 50.000</li>
                    </ul>'),
                'type'             => 'pack',
                'price' => [
                    'month' => 0
                ]
            ],
            [
                'id'               => 'PROFESSIONAL',
                'name'             => _('Professional pack'),
                'description'      => _('Move your newspaper to the next level. Enables you to start to raise money with it.'),
                'long_description' => _('<p>Our best selling solution, it allows to manage a professional newspaper and start gaining money with it!</p>
                        <p>This offer gives you more than 40% discount (if purchased separately modules have a value of 85EUR/month)</p>
                        This pack includes:
                        <ul>
                            <li>Frontpage manager</li>
                            <li>Widget manager</li>
                            <li>Opinion articles manager</li>
                            <li>Comments manager</li>
                            <li>Images and files uploading</li>
                            <li>Utilities: Trash, Search Advanced...</li>
                            <li>Advertisement manager</li>
                            <li>Polls manager</li>
                            <li>Galleries manager</li>
                            <li>Video manager</li>
                            <li>1 user license</li>
                            <li>Support via tickets</li>
                            <li>Media storage: 1GB</li>
                            <li>Page views: 100.000</li>
                        </ul>'),
                'type'             => 'pack',
                'price' => [
                    'month' => 50
                ]
            ],
            [
                'id'               => 'SILVER',
                'name'             => _('Silver pack'),
                'description'      => _('Silver pack'),
                'long_description' => _('<p>Personalize your frontpages and start sending newsletters
                    to your readers and let them know what they have missed!</p>
                    <p>This offer gives you more than 30% discount on modules (if purchased
                    separately modules have a value of 145EUR/month).</p>
                    This pack includes:
                    <ul>
                        <li>Frontpage manager</li>
                        <li>Widget manager</li>
                        <li>Opinion articles manager</li>
                        <li>Comments manager</li>
                        <li>Images and files uploading</li>
                        <li>Utilities: Trash, Search Advanced...</li>
                        <li>Advertisement manager</li>
                        <li>Polls manager</li>
                        <li>Galleries manager</li>
                        <li>Video manager</li>
                        <li>Frontpage customization</li>
                        <li>Newsletter manager (*)</li>
                        <li>2 user license</li>
                        <li>Support via tickets</li>
                        <li>Support via phone: 4h (10am-2pm M-F)</li>
                        <li>Media storage: 1.5GB</li>
                        <li>Page views: 250.000</li>
                    </ul>
                    <p><small>*  Newsletter manager: email sendings are charged with 0.3€ each block of 1000 sent emails</small></p>'),
                'type'             => 'pack',
                'price' => [
                    'month' => 250
                ]
            ],
            [
                'id'               => 'GOLD',
                'name'             => _('Gold pack'),
                'description'      => _('Gold pack'),
                'long_description' => _('<p>Personalize your frontpages and start sending newsletters
                    to your readers and let them know what they have missed!</p>
                    <p>This offer gives you more than 30% discount on modules (if purchased
                    separately modules have a value of 145EUR/month).</p>
                    This pack includes:
                    <ul>
                        <li>Frontpage manager</li>
                        <li>Widget manager</li>
                        <li>Opinion articles manager</li>
                        <li>Comments manager</li>
                        <li>Images and files uploading</li>
                        <li>Utilities: Trash, Search Advanced...</li>
                        <li>Advertisement manager</li>
                        <li>Polls manager</li>
                        <li>Galleries manager</li>
                        <li>Video manager</li>
                        <li>Frontpage customization</li>
                        <li>Newsletter manager (*)</li>
                        <li>5 user license</li>
                        <li>Support via tickets</li>
                        <li>Support via phone: 8h (10am-6pm M-F)</li>
                        <li>Media storage: 2.5GB</li>
                        <li>Page views: 500.000</li>
                    </ul>
                    <p><small>*  Newsletter manager: email sendings are charged with 0.3€ each block of 1000 sent emails</small></p>'),
                'type'             => 'pack',
                'price' => [
                    'month' => 500
                ]
            ]
        ];

        return new JsonResponse(
            [ 'results' => array_merge($modules, $packs), 'activated' => $activated ]
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
