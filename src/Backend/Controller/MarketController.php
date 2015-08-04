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

        if (!empty($instance->metas)
            && array_key_exists('billing', $instance->metas)
        ) {
            $billing = $instance->metas['billing'];
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
