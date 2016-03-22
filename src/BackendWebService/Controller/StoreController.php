<?php

namespace BackendWebService\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Framework\ORM\Entity\Extension;

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

        // Fetch user data
        $modulesRequested = $request->request->get('modules');
        $billing          = $request->request->get('billing');

        // Fetch information about modules
        $availableItems = [];
        $modules   = $this->get('orm.manager')
            ->getRepository('manager.extension')
            ->findBy([
                'enabled' => [ [ 'value' => 1 ] ],
                'type'    => [ 'union' => 'OR', [ 'value' => 'module' ], [ 'value' => 'theme-addon' ] ]
            ]);
        $packs     = \Onm\Module\ModuleManager::getAvailablePacks();
        $themes    = $this->get('orm.loader')->getPlugins();

        foreach ($modules as $module) {
            $availableItems[$module->uuid] =
                array_key_exists(CURRENT_LANGUAGE_SHORT, $module->name) ?
                $module->name[CURRENT_LANGUAGE_SHORT] :
                $module->name['en'];
        }

        foreach ($packs as $pack) {
            $availableItems[$pack['id']] = $pack['name'];
        }

        foreach ($themes as $theme) {
            $availableItems[$theme->uuid] = $theme->name;
        }

        $instance = $this->get('instance');

        // Save new billing info for instance
        foreach ($billing as $key => $value) {
            $instance->metas['billing_' . $key] = $value;
        }

        $this->get('instance_manager')->persist($instance);

        // Get names for filtered modules to use in template
        $modulesRequested = array_filter($availableItems, function($key, $value) use($modulesRequested) {
            return in_array($value, $modulesRequested);
        }, ARRAY_FILTER_USE_BOTH);

        // Send emails
        $this->sendEmailToSales($billing, $modulesRequested, $instance);
        $this->sendEmailToCustomer($modulesRequested, $instance);

        $this->get('application.log')->info(
            'The user ' . $this->getUser()->username
            . '(' . $this->getUser()->id  .') has purchased '
            . implode(', ', $modulesRequested)
        );

        return new JsonResponse(_('Your request has been registered'));
    }

    /**
     * Checks a phone number.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function checkPhoneAction(Request $request)
    {
        $code      = 200;
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        $country = $request->query->get('country');
        $phone   = $request->query->get('phone');

        try {
            $numberProto = $phoneUtil->parse($phone, $country);
            if (!$phoneUtil->isValidNumber($numberProto)) {
                $code = 400;
            }
        } catch (\Exception $e) {
            $code = 400;
        }

        return new JsonResponse('', $code);
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
        $modules   = $this->get('orm.manager')
            ->getRepository('manager.extension')
            ->findBy([
                'enabled' => [ [ 'value' => 1 ] ],
                'type'    => [ [ 'value' => 'module' ] ]
            ]);

        $activated = $this->get('instance')->activated_modules;

        if (in_array('ALBUM_MANAGER', $activated)
            && in_array('VIDEO_MANAGER', $activated)
        ) {
            $activated[] = 'MEDIA_MANAGER';
        }

        $modules = array_map(function (&$a) {
            foreach ([ 'about', 'description', 'name' ] as $key) {
                if (!empty($a->{$key})) {
                    $lang = $a->{$key}['en'];

                    if (array_key_exists(CURRENT_LANGUAGE_SHORT, $a->{$key})
                        && !empty($a->{$key}[CURRENT_LANGUAGE_SHORT])
                    ) {
                        $lang = $a->{$key}[CURRENT_LANGUAGE_SHORT];
                    }

                    $a->{$key} = $lang;
                }
            }

            if (array_key_exists('price', $a->metas)) {
                $a->price = $a->metas['price'];
            }

            return $a->getData();
        }, $modules);

        return new JsonResponse(
            [ 'results' => $modules, 'activated' => $activated ]
        );
    }

    /**
     * Saves the billing information.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveBillingAction(Request $request)
    {
        $billing  = $request->request->all();
        $instance = $this->get('instance');

        foreach ($billing as $key => $value) {
            $instance->metas['billing_' . $key] = $value;
        }

        $this->get('instance_manager')->persist($instance);

        return new JsonResponse(_('Billing information saved successfully'));
    }

    /**
     * Sends an email to the customer.
     *
     * @param array    $modules  The requested modules.
     * @param Instance $instance The instance to upgrade.
     */
    private function sendEmailToCustomer($modules, $instance)
    {
        $params = $this->container
            ->getParameter("manager_webservice");

        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Store purchase request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
            ->setTo($this->getUser()->contact_mail)
            ->setBody(
                $this->renderView(
                    'store/email/_purchaseToCustomer.tpl',
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
     * @param array    $billing  The billing information.
     */
    private function sendEmailToSales($billing, $modules, $instance)
    {
        $params = $this->container
            ->getParameter("manager_webservice");

        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Store purchase request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
            ->setTo($this->container->getParameter('sales_email'))
            ->setBody(
                $this->renderView(
                    'store/email/_purchaseToSales.tpl',
                    [
                        'billing'  => $billing,
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
