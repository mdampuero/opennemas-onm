<?php

namespace BackendWebService\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class MarketController extends Controller
{
    public function listAction()
    {
        $modules = \Onm\Module\ModuleManager::getAvailableModulesGrouped();

        return new JsonResponse([ 'results' => $modules ]);
    }
}
