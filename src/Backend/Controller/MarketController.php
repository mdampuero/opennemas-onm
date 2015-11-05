<?php

namespace Backend\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Intl;

class MarketController extends Controller
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

        $countries = Intl::getRegionBundle()->getCountryNames();

        return $this->render(
            'market/checkout.tpl',
            [ 'billing' => $billing, 'countries' => $countries ]
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

        return $this->render('market/list.tpl', [ 'plans' => $plans ]);
    }
}
