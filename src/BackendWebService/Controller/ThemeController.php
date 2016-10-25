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

        $exclusive = $this->getAvailableThemes();

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
                    'Get a customized newspaper Web Site, with widgets included, so that everyone will recognize your brand and image.'
                ),
                'long_description' => _(
                    '<p>Newspaper Web Site Template that can be customized to reflect better brand guidelines and customer preferences</p>
                    <ul>
                        <li>Widgets: Standard widgets included. To add a widget please contact us at <a href="mailto:sales@openhost.es">sales@openhost.es</a></li>
                        <li>Exclusivity: This template is not exclusive</li>
                        <li>Delivery time: From 2 weeks up to 1 month depending on customization work</li>
                        <li>Change request BEFORE launch
                            <ul>
                                <li>Changes included: typography, newspaper colours and style</li>
                                <li>Changes NOT included: Widgets, Menus, Titles, Pretitle, Inner Article
                                    Disposition, Images Size, Headers and footers</li>
                                <li>1 iteration of feedback and change request included before production</li>
                            </ul>
                        </li>
                        <li>Change request AFTER launch
                            <ul>
                                <li>1 iteration of feedback and change request only included
                                    in 30 days post production</li>
                                <li>Monitoring and Bug fixing (if any) included 30 days post production</li>
                            </ul>
                        </li>
                        <li>Add On:
                            <ul>
                                <li>New widgets: 120€ each</li>
                                <li>Get newspaper one week in advance: 500€</li>
                                <li>Support cost after launch</li>
                            </ul>
                        </li>
                    </ul>'
                ),
                'price' => [
                    [ 'value' => 130, 'type' =>  'monthly' ],
                    [ 'value' => 1450, 'type' => 'single' ]
                ]
            ],
            [
                'id'               => 'EXCLUSIVE_TEMPLATE',
                'type'             => 'theme',
                'thumbnail'        => 'theme-exclusive.jpg',
                'name'             => _('Exclusive Template'),
                'description'      => _(
                    'Unique Newspaper Web Site  with many widgets, completely customizable and in exclusive development for the customer.'
                ),
                'long_description' => _(
                    '<ul>
                        <li>Type of exclusive templates:
                            <ul>
                                <li>Newspaper Web Site Template that can be customized to reflect better brand guidelines and customer preferences</li>
                                <li>The template will be developed following mockups submitted by customer</li>
                                <li>Customer requires that new web site looks like previous newspaper (migration)</li>
                            </ul>
                        </li>
                        <li>Widgets
                            <ul>
                                <li>
                                    Standard widgets included. To add a widget please contact us at
                                    <a href="mailto:sales@openhost.es">sales@openhost.es</a>
                                </li>
                                <li>
                                    1 additional widget included
                                </li>
                            </ul>

                        </li>
                        <li>Exclusivity: This template is exclusive. The template will not be available for any other newspapers.</li>
                        <li>Delivery time: 2 months</li>
                        <li>Change request BEFORE launch
                            <ul>
                                <li>Changes included: typography, newspaper colours and style</li>
                                <li>Changes NOT included: Widgets, Menus, Titles, Pretitle, Inner Article Disposition, Images Size, Headers and footers</li>
                                <li>Pages available: Frontpages, Opinions, Authors, Inner Article, Inner Media( Images, Gallery, Video), Inner Polls</li>
                                <li>NOTE: 2 iterations of feedback and change request included before production</li>
                            </ul>
                        </li>
                        <li>Change request AFTER launch
                            <ul>
                                <li>2 iteration of feedback and change request only included in 30 days post production</li>
                                <li>Monitoring and Bug fixing (if any) included 30 days post production</li>
                            </ul>
                        </li>
                        <li>Add On:
                            <ul>
                                <li>New widgets: 120€ each</li>
                                <li>Get newspaper one week in advance: 500€</li>
                                <li>Creation of new page over included or over time 1500€</li>
                                <li>
                                    Support cost after launch
                                    <a href="http://help.opennemas.com/knowledgebase/articles/463594-precios-opennemas-servicio-de-desarrollo" target="_blank">
                                        http://help.opennemas.com/knowledgebase/articles/463594-precios-opennemas-servicio-de-desarrollo
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>'
                ),
                'price' => [
                    [ 'value' => 350, 'type' =>  'monthly' ],
                    [ 'value' => 3500, 'type' => 'single' ]
                ]
            ],
            [
                'id'               => 'CUSTOM_EXCLUSIVE_TEMPLATE',
                'type'             => 'theme',
                'thumbnail'        => 'theme-exclusiveplus.jpg',
                'name'             => _('Custom Exclusive Template'),
                'description'      => _('Newspaper web site developed from scratch by Opennemas team exclusively for you.'),
                'long_description' => _(
                    '<ul>
                        <li>Widgets: all required widgets included</li>
                        <li>Exclusivity: This template is exclusive. The template will
                            not be available for any other newspapers.</li>
                        <li>Delivery time: To establish at the beginning of the project depending on requirements</li>
                        <li>Change request BEFORE launch
                            <ul>
                                <li>Changes included: typography, newspaper colors and
                                    style, menus, openings and grids, inner pages</li>
                                <li>Pages available: all pages supported by opennemas modules</li>
                                <li>3 iterations of feedback and change request included before production</li>
                            </ul>
                        </li>
                        <li>Change request AFTER launch
                            <ul>
                                <li>3 iteration of feedback and change request only included
                                    in 30 days post production</li>
                                <li>Monitoring and Bug fixing (if any) included 30 days post production</li>
                            </ul>
                        </li>
                        <li>Cost:
                            <ul>
                                <li>Design: from 3,000€ to 15,000€ (each month of design work)</li>
                                <li>Development: from 3,000€ to 6,000€ (each moth of development) </li>
                                <li>Opennemas Template Creation: from 3,000€ to 6,000€ (each month of templating work)</li>
                            </ul>
                        <li>Add On:
                            <ul>
                                <li>New widgets: 120€ each</li>
                                <li>Get newspaper one week in advance: 500€</li>
                                <li>Creation of new page over included or over time 1500€</li>
                                <li>
                                    Support cost after launch
                                    <a href="http://help.opennemas.com/knowledgebase/articles/463594-precios-opennemas-servicio-de-desarrollo" target="_blank">
                                        http://help.opennemas.com/knowledgebase/articles/463594-precios-opennemas-servicio-de-desarrollo
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>'
                ),
                'price' => [
                    [ 'value' => 350, 'type' =>  'monthly' ],
                    [ 'value' => 3500, 'type' => 'single' ]
                ]
            ],
        ];
    }
}
