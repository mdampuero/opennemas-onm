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

        // Fetch information about modules
        $availableItems = [];

        $oql     = 'enabled = 1 and (type = "module" or type = "theme-addon")';
        $modules = $this->get('orm.manager')
            ->getRepository('Extension')
            ->findBy($oql);
        $packs     = \Onm\Module\ModuleManager::getAvailablePacks();
        $themes    = $this->get('orm.manager')->getRepository('Theme')
            ->findBy();

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

        $instance = $this->get('core.instance');
        $client = $this->get('orm.manager')
            ->getRepository('Client', 'manager')
            ->find($instance->getClient());

        // Get names for filtered modules to use in template
        $modulesRequested = array_intersect_key($availableItems, array_flip($modulesRequested));

        // Send emails
        $this->sendEmailToSales($client, $modulesRequested);
        $this->sendEmailToCustomer($client, $modulesRequested);

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
            if (!$vat->validate($country, $vatNumber)
                && array_key_exists($country, $vat->getTaxes())
            ) {
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
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('Extension');
        $modules   = $em->getRepository('Extension')
            ->findBy('enabled = 1 and type = "module"');

        $activated = $this->get('core.instance')->activated_modules;

        if (in_array('ALBUM_MANAGER', $activated)
            && in_array('VIDEO_MANAGER', $activated)
        ) {
            $activated[] = 'MEDIA_MANAGER';
        }

        $modules = $converter->responsify($modules);

        $modules = array_map(function (&$a) {
            foreach ([ 'about', 'description', 'name' ] as $key) {
                if (!empty($a[$key])) {
                    $lang = $a[$key]['en'];

                    if (array_key_exists(CURRENT_LANGUAGE_SHORT, $a[$key])
                        && !empty($a[$key][CURRENT_LANGUAGE_SHORT])
                    ) {
                        $lang = $a[$key][CURRENT_LANGUAGE_SHORT];
                    }

                    $a[$key] = $lang;
                }
            }

            return $a;
        }, $modules);

        return new JsonResponse(
            [ 'results' => $modules, 'activated' => $activated ]
        );
    }

    /**
     * Sends an email to the customer.
     *
     * @param Client $client  The client.
     * @param array  $modules The requested modules.
     */
    private function sendEmailToCustomer($client, $modules)
    {
        $instance = $this->get('core.instance');
        $params   = $this->getParameter('manager_webservice');

        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Store purchase request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
            ->setTo($client->email)
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

        if ($instance->contact_mail !== $client->email) {
            $message->setBcc($instance->contact_mail);
        }

        $this->get('mailer')->send($message);
    }

    /**
     * Sends an email to sales department.
     *
     * @param Client $client  The client information.
     * @param array  $modules The requested modules.
     */
    private function sendEmailToSales($client, $modules)
    {
        $instance = $this->get('core.instance');
        $params   = $this->getParameter('manager_webservice');

        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Store purchase request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
            ->setTo($this->container->getParameter('sales_email'))
            ->setBody(
                $this->renderView(
                    'store/email/_purchaseToSales.tpl',
                    [
                        'client'   => $client,
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
