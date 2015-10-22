<?php

namespace Backend\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class MarketController extends Controller
{
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

        return $this->render('market/checkout.tpl', [ 'billing' => $billing ]);
    }

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
