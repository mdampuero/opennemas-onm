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
use Onm\Instance\InstanceManager as im;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ThemeController extends Controller
{
    /**
     * Enables a theme.
     *
     * @param string $id The theme UUID.
     *
     * @return JsonResponse The response object.
     */
    public function enableAction($uuid)
    {
        $instance = $this->get('instance');
        $themes = im::getAvailableTemplates();

        $theme = str_replace('es.openhost.theme.', '', $uuid);

        if (!in_array($theme, $themes)) {
            return new JsonResponse(_('Invalid theme'), 400);
        }

        $instance->settings['TEMPLATE_USER'] = $uuid;

        $this->get('instance_manager')->persist($instance);

        dispatchEventWithParams('theme.change');

        return new JsonResponse();
    }

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
            $theme['description'] = null;
        }

        $addons = $this->get('orm.manager')->getRepository('manager.extension')
            ->findBy([ 'type' => [ [ 'value' => 'theme-addon'] ] ]);

        foreach ($addons as &$addon) {
            $addon->about       = $addon->about[CURRENT_LANGUAGE_SHORT];
            $addon->description = $addon->description[CURRENT_LANGUAGE_SHORT];
            $addon->name        = $addon->name[CURRENT_LANGUAGE_SHORT];

            $prices = array_filter($addon->metas['price'], function ($a) {
                return $a['type'] === 'monthly';
            });

            $addon->screenshots = $addon->images;
            $addon->price       = [ 'month' => array_pop($prices)['value'] ];

            $addon = $addon->getData();
        }

        $exclusive = \Onm\Module\ModuleManager::getAvailableThemes();
        array_shift($exclusive);

        $instance  = $this->get('instance');
        $purchased = [];

        if (array_key_exists('purchased', $instance->metas)) {
            $purchased = $this->get('instance')->metas['purchased'];
        }

        $active = 'es.openhost.theme.' . str_replace(
            'es.openhost.theme.',
            '',
            $instance->settings['TEMPLATE_USER']
        );

        return new JsonResponse(
            [
                'active'    => $active,
                'addons'    => $addons,
                'exclusive' => $exclusive,
                'purchased' => $purchased,
                'themes'    => $themes
            ]
        );
    }
}
