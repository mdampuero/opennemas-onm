<?php

namespace Backend\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class MarketController extends Controller
{
    public function listAction()
    {
        $plans = \Onm\Module\ModuleManager::getAvailablePacks();
        return $this->render('market/list.tpl', [ 'plans' => $plans ]);
    }
}
