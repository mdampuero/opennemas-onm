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

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        $themes = $em->getRepository('theme', 'file')->findBy("order by name asc");
        $themes = $converter->responsify($themes, true);

        $addons = $repository->findBy('enabled=1 and type="theme-addon"');
        $addons = $converter->responsify($addons, true);

        $custom = $repository->findOneBy('uuid="es.openhost.theme.customization"');
        $custom = $converter->responsify($custom, true);

        $exclusive = $this->getAvailableThemes();

        return new JsonResponse([
            'active'        => $this->get('core.theme')->uuid,
            'addons'        => $addons,
            'customization' => $custom,
            'exclusive'     => $exclusive,
            'purchased'     => $purchased,
            'themes'        => $themes
        ]);
    }

    /**
     * Returns an array with all available themes for market.
     *
     * @return array The array of themes for market.
     */
    protected function getAvailableThemes()
    {
        return [
            [
                'id'               => 'CUSTOM_TEMPLATE',
                'type'             => 'theme',
                'thumbnail'        => 'theme-custom.jpg',
                'name'             => _('Custom Template'),
                'description'      => _(
                    'Get a customized newspaper Web Site, with widgets included, '
                    . 'so that everyone will recognize your brand and image.'
                ),
                'price' => [
                    [ 'value' => 130, 'type' => 'monthly' ],
                    [ 'value' => 1450, 'type' => 'single' ]
                ]
            ],
            [
                'id'               => 'EXCLUSIVE_TEMPLATE',
                'type'             => 'theme',
                'thumbnail'        => 'theme-exclusive.jpg',
                'name'             => _('Exclusive Template'),
                'description'      => _(
                    'Unique Newspaper Web Site  with many widgets, completely '
                    . 'customizable and in exclusive development for the customer.'
                ),
                'price' => [
                    [ 'value' => 350, 'type' => 'monthly' ],
                    [ 'value' => 3500, 'type' => 'single' ]
                ]
            ],
            [
                'id'               => 'CUSTOM_EXCLUSIVE_TEMPLATE',
                'type'             => 'theme',
                'thumbnail'        => 'theme-exclusiveplus.jpg',
                'name'             => _('Custom Exclusive Template'),
                'description'      => _('Newspaper web site developed from scratch by '
                    . 'Opennemas team exclusively for you.'),
                'price' => [
                    [ 'value' => 350, 'type' => 'monthly' ],
                    [ 'value' => 3500, 'type' => 'single' ]
                ]
            ],
        ];
    }
}
