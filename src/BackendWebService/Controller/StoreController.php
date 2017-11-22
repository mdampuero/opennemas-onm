<?php

namespace BackendWebService\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Annotation\Security;

class StoreController extends Controller
{
    /**
     * Request a modules purchase to the sales department.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('ROLE_ADMIN')")
     */
    public function checkoutAction(Request $request)
    {
        $purchase = $request->request->get('purchase');
        $nonce    = $request->request->get('nonce');

        try {
            $ph = $this->get('core.helper.checkout');

            $ph->getPurchase($purchase);

            if (!empty($nonce) && $ph->getPurchase()->total > 0) {
                $ph->pay($nonce);
            }

            $purchase = $ph->getPurchase();

            $ph->sendEmailToClient();
            $ph->sendEmailToSales();

            $ph->enable();

            $this->get('application.log')->info(
                'The user ' . $this->getUser()->username
                . '(' . $this->getUser()->id . ') has purchased '
                . json_encode($purchase->details)
            );
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return new JsonResponse([
                'message' => $e->getMessage(),
                'type' => 'error'
            ], 400);
        }

        return new JsonResponse(_('Purchase completed!'));
    }

    /**
     * Checks a phone number.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('ROLE_ADMIN')")
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
     *
     * @Security("hasPermission('ROLE_ADMIN')")
     */
    public function checkVatAction(Request $request)
    {
        $code = 200;
        $vat  = $this->get('vat');

        $country   = $request->query->get('country');
        $region    = $request->query->get('region');
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

        $vatValue = $vat->getVatFromCode($country, $region);

        return new JsonResponse($vatValue, $code);
    }

    /**
     * Returns the list of modules and current activated modules.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('ROLE_ADMIN')")
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
            foreach ([ 'about', 'description', 'name', 'terms', 'notes' ] as $key) {
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
}
