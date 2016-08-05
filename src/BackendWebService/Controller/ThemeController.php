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
        $em       = $this->get('orm.manager');
        $instance = $this->get('core.instance');

        // Check if theme exists
        $em->getRepository('Theme')->findOneBy('uuid = ' . $uuid);

        $instance->settings['TEMPLATE_USER'] = $uuid;

        $em->persist($instance);
        $this->get('core.dispatcher')
            ->dispatch('instance.update', [ 'instance' => $instance ]);

        return new JsonResponse();
    }

    /**
     * Returns the list of themes.
     *
     * @return JsonResponse The response object.
     */
    public function listAction()
    {
        $themes = $this->get('orm.manager')->getRepository('theme', 'file')
            ->findBy();

        foreach ($themes as &$theme) {
            $theme = $theme->getData();
            $theme['description'] = null;
        }

        $addons = $this->get('orm.manager')
            ->getRepository('extension', 'database')
            ->findBy('enabled = 1 and type = "theme-addon"');

        foreach ($addons as &$addon) {
            $addon->about       = array_key_exists(CURRENT_LANGUAGE_SHORT, $addon->about)
                ? $addon->about[CURRENT_LANGUAGE_SHORT]
                : $addon->about['en'];
            $addon->description = array_key_exists(CURRENT_LANGUAGE_SHORT, $addon->description)
                ? $addon->description[CURRENT_LANGUAGE_SHORT]
                : $addon->description['en'];
            $addon->name        = array_key_exists(CURRENT_LANGUAGE_SHORT, $addon->name)
                ? $addon->name[CURRENT_LANGUAGE_SHORT]
                : $addon->name['en'];

            if ($addon->metas && array_key_exists('price', $addon->metas)) {
                $addon->price = $addon->metas['price'];
            }

            $addon = $addon->getData();
        }

        $exclusive = \Onm\Module\ModuleManager::getAvailableThemes();
        array_shift($exclusive);

        $instance = $this->get('core.instance');
        $active   = 'es.openhost.theme.' . str_replace(
            'es.openhost.theme.',
            '',
            $instance->settings['TEMPLATE_USER']
        );

        return new JsonResponse(
            [
                'active'    => $active,
                'addons'    => $addons,
                'exclusive' => $exclusive,
                'purchased' => empty($instance->purchased) ? [] : $instance->purchased,
                'themes'    => $themes
            ]
        );
    }
}
