<?php

namespace Backend\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Intl;

class StoreController extends Controller
{
    /**
     * Displays the wizard form for checkout.
     *
     * @return Response The response object.
     */
    public function checkoutAction()
    {
        $billing = [];

        $instance = $this->get('instance');

        if (!empty($instance->metas)) {
            foreach ($instance->metas as $key => $value) {
                if (strpos($key, 'billing_') !== false) {
                    $billing[str_replace('billing_', '', $key)] = $value;
                }
            }
        }

        $countries = array_flip(Intl::getRegionBundle()->getCountryNames());
        $taxes     = $this->get('vat')->getTaxes();

        return $this->render(
            'store/checkout.tpl',
            [
                'billing'   => $billing,
                'countries' => $countries,
                'taxes'     => $taxes
            ]
        );
    }

    /**
     * Displays the list of available modules.
     *
     * @return Response The response object.
     */
    public function listAction()
    {
        $plans = \Onm\Module\ModuleManager::getAvailablePacks();
        $plans[] = [
            'id'   => 'OTHER',
            'name' => _('Others')
        ];

        return $this->render('store/list.tpl', [ 'plans' => $plans ]);
    }
}
