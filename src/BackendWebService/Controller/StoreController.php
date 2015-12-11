<?php

namespace BackendWebService\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StoreController extends Controller
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
        $packs     = \Onm\Module\ModuleManager::getAvailablePacks();
        $themes    = $this->get('orm.loader')->getPlugins();

        foreach ($packs as $pack) {
            $available[$pack['id']] = $pack['name'];
        }

        foreach ($themes as $theme) {
            $available[$theme->uuid] = $theme->name;
        }

        $instance = $this->get('instance');
        $modules  = $request->request->get('modules');
        $billing  = $request->request->get('billing');

        $instance = $this->get('instance');

        foreach ($billing as $key => $value) {
            $instance->metas['billing_' . $key] = $value;
        }

        $this->get('instance_manager')->persist($instance);

        // Filter request to ignore invalid modules
        $modules = array_filter($modules, function ($e) use ($available) {
            return array_key_exists($e, $available);
        });

        // Get names for filtered modules to use in template
        $purchased = array_intersect_key($available, array_flip($modules));

        $this->sendEmailToSales($instance, $purchased);
        $this->sendEmailToCustomer($instance, $purchased);

        $this->get('application.log')->info(
            'The user ' . $this->getUser()->username
            . '(' . $this->getUser()->id  .') has purchased '
            . implode(', ', $modules)
        );

        return new JsonResponse(_('Your request has been registered'));
    }

    /**
     * Checks a VAT number.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function checkVatAction(Request $request)
    {
        $code  = 200;
        $vat   = $this->get('vat');

        $country   = $request->query->get('country');
        $vatNumber = $request->query->get('vat');

        try {
            if (!$vat->validate($country, $vatNumber)) {
                $code = 400;
            }
        } catch (\Exception $e) {
            $code = 400;
        }

        $vatValue = $vat->getVatFromCode($country);

        return new JsonResponse($vatValue, $code);
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

            // Remove ALBUM_MANAGER, PHOTO_MANAGER and VIDEO_MANAGER
            if (array_key_exists('id', $a)
                && ($a['id'] === 'ALBUM_MANAGER'
                    || $a['id'] === 'VIDEO_MANAGER')
            ) {
                return false;
            }

            return true;
        });

        array_push(
            $modules,
            [
                'id'               => 'MEDIA_MANAGER',
                'plan'             => 'PROFESSIONAL',
                'name'             => _('Media'),
                'thumbnail'        => 'module-multimedia.jpg',
                'description'      => _('Add Video and Image Galleries to your content. '),
                'long_description' => _('<p>This module will allow you to create Photo Galleries, add video from YouTube, Vimeo, Dailymotion and from other 10 sources more.</p><p>And the most interesting fact is that the video manager is the same as youtube one, perfect consistency and performance.</p>'),
                'type'             => 'module',
                'price' => [
                    'month' => 35
                ]
            ]
        );

        $packs = \Onm\Module\ModuleManager::getAvailablePacks();
        $themes = \Onm\Module\ModuleManager::getAvailableThemes();

        $results = array_merge($modules, $packs, $themes);
        foreach ($results as &$result) {
            if (empty($result['author'])) {
                $result['author'] = '<a href="https://www.opennemas.com/about" target="_blank">Opennemas</a>';
            }
        }

        return new JsonResponse(
            [ 'results' => $results, 'activated' => $activated ]
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
        $params = $this->container
            ->getParameter("manager_webservice");

        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Market purchase request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
            ->setTo($this->getUser()->contact_mail)
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
        $params = $this->container
            ->getParameter("manager_webservice");

        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Market purchase request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
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
