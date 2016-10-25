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
        $em         = $this->get('orm.manager');
        $repository = $em->getRepository('extension', 'database');
        $converter  = $em->getConverter('Extension');
        $purchased  = [];

        if (!empty($this->get('core.instance')->purchased)) {
            $purchased = $this->get('core.instance')->purchased;
        }

        $themes = $em->getRepository('theme', 'file')->findBy();
        $themes = $converter->responsify($themes, true);

        $addons = $repository->findBy('enabled=1 and type="theme-addon"');
        $addons = $converter->responsify($addons, true);

        $custom = $repository->findOneBy('uuid="es.openhost.theme.customization"');
        $custom = $converter->responsify($custom, true);

        $exclusive = \Onm\Module\ModuleManager::getAvailableThemes();
        array_shift($exclusive);

        return new JsonResponse([
            'active'        => $this->get('core.theme')->uuid,
            'addons'        => $addons,
            'customization' => $custom,
            'exclusive'     => $exclusive,
            'purchased'     => $purchased,
            'themes'        => $themes
        ]);
    }
}
