<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ThemeController extends Controller
{
    /**
     * Returns the list of themes.
     *
     * @return JsonResponse The response object.
     */
    public function listAction()
    {
        $themes = $this->get('orm.loader')->getPlugins();

        foreach ($themes as &$theme) {
            $theme = $theme->getData();
        }

        $exclusive = \Onm\Module\ModuleManager::getAvailableThemes();
        array_shift($exclusive);

        $myThemes  = [ 'theme1', 'theme2' ];
        $enabled   = [ 'theme1' ];

        return new JsonResponse(
            [
                'themes'    => $themes,
                'enabled'   => $enabled,
                'exclusive' => $exclusive,
                'myThemes'  => $myThemes
            ]
        );
    }
}
