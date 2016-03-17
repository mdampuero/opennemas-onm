<?php
/**
 * Defines the Onm\Module\ModuleManager class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_Module
 */
namespace Onm\Module;

use Onm\Security\Acl;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class for handling activated and available modules.
 *
 * @package    Onm_Module
 */
class ModuleManager
{
    /**
     * Stores the activatedModules over all instances of ModuleManager
     *
     * @var array
     **/
    public static $activatedModules = null;

    /**
     * Stores all the available modules over all instances of ModuleManager
     *
     * @var array
     **/
    public static $availableModules = null;

    /**
     * Stores modules with all the modules available grouped by plan
     *
     * @var array
     **/
    public static $availableModulesGrouped = null;

    /**
     * Initilizes the object.
     *
     * @param array $params parameters for initializing the module manager.
     */
    public function __construct()
    {
        self::getActivatedModules();
    }

    /**
     * Returns the activated modules.
     *
     * @return array array of activated modules
     */
    public static function getActivatedModules()
    {
        //global $activatedModules;

        $activatedModules = getService('instance_manager')->current_instance
            ->activated_modules;

        if (is_null(self::$activatedModules)) {

            if (!isset($activatedModules) or (count($activatedModules) < 1)) {

                self::$activatedModules = self::getAvailableModules();

            } elseif (self::checkAllModulesActivated()) {

                self::$activatedModules = self::getAvailableModules();

            } else {

                self::$activatedModules = $activatedModules;

            }
        }

        return self::$activatedModules;
    }

    /**
     * Returns changes in modules.
     *
     * @return array array of changes in modules
     */
    public static function getChangesInModules()
    {
        $changesInModules = getService('instance_manager')->current_instance
            ->changes_in_modules;

        return $changesInModules;
    }

    /**
     * Returns the description for the module.
     *
     * @return string The module description
     */
    public static function getModuleDescription($moduleName)
    {
        $modules = self::getAvailableModulesGrouped();

        $description = '';
        foreach ($modules as $module) {
            if ($module['id'] == $moduleName) {
                $description = $module['description'];
                break;
            }
        }

        return $description;
    }

    /**
     * Returns the list of module ids for pack.
     *
     * @param string $pack The pack id.
     *
     * @return array The list of module id.
     */
    public static function getModuleIdsByPack($pack)
    {
        $modules = self::getAvailableModulesGrouped();

        return array_map(function ($module) {
            return $module['id'];
        }, array_filter($modules, function ($module) use ($pack) {
            return $module['plan'] === $pack;
        }));
    }

    /**
     * Returns the list of available modules in Onm instance.
     *
     * @return array the list of available modules
     */
    public static function getAvailableModules()
    {
        if (!isset(self::$availableModules)) {
            self::$availableModules = array(
                'AMP_MODULE'                 => _('Accelerated Mobile Pages integration'),
                'ADS_MANAGER'                => _('Advertisement'),
                'ADVANCED_SEARCH'            => _('Advanced search'),
                'ALBUM_MANAGER'              => _('Albums'),
                'ARTICLE_MANAGER'            => _('Articles'),
                'ADVANCED_ARTICLE_MANAGER'   => _('Advanced article options'),
                'ADVANCED_FRONTPAGE_MANAGER' => _('Frontpage Customization'),
                'BLOG_MANAGER'               => _('Authors Blog'),
                'BOOK_MANAGER'               => _('Books'),
                'CACHE_MANAGER'              => _('Cache manager'),
                'CATEGORY_MANAGER'           => _('Category'),
                'COMMENT_MANAGER'            => _('Comments'),
                'CONTENT_SUBSCRIPTIONS'      => _('Subscription'),
                'CRONICAS_MODULES'           => _('Cronicas customizations'),
                'FILE_MANAGER'               => _('Files'),
                'FORM_MANAGER'               => _('Contact'),
                'FRONTPAGE_MANAGER'          => _('Frontpages'),
                'FRONTPAGES_LAYOUT'          => _('Frontpages Manager'),
                'IMAGE_MANAGER'              => _('Images'),
                'IADBOX_MANAGER'             => _('iadbox'),
                'XML_IMPORT'                 => _('Import XMLs'),
                'KEYWORD_MANAGER'            => _('Keywords'),
                'KIOSKO_MANAGER'             => _('NewsStand'),
                'LETTER_MANAGER'             => _('Letters'),
                'LIBRARY_MANAGER'            => _('Library'),
                'MENU_MANAGER'               => _('Menus'),
                'NEWS_AGENCY_IMPORTER'       => _('News Agency importer'),
                'NEWSLETTER_MANAGER'         => _('Newsletter'),
                'OPENNEMAS_AGENCY'           => _('Articles Synchronization'),
                'OPINION_MANAGER'            => _('Opinion'),
                'POLL_MANAGER'               => _('Polls'),
                'PROMOTIONAL_BAR'            => _('Promotional bar'),
                'SCHEDULE_MANAGER'           => _('Schedules'),
                'SETTINGS_MANAGER'           => _('System wide settings'),
                'SPECIAL_MANAGER'            => _('Specials'),
                'STATIC_LIBRARY'             => _('Static library'),
                'STATIC_PAGES_MANAGER'       => _('Static pages'),
                'SUPPORT_NONE'               => _('Basic support'),
                'SUPPORT_TRAINING'           =>_('Training and Advisory Services'),
                'SUPPORT_PRO'                => _('Profesional support'),
                'SUPPORT_2'                  => _('Support 2'),
                'SUPPORT_3'                  => _('Support 3'),
                'SUPPORT_4'                  => _('Support 4'),
                'SUPPORT_8'                  => _('Support 8'),
                'SUPPORT_8_PLUS'             => _('Support 8+'),
                'SYNC_MANAGER'               => _('Instance synchronization'),
                'TRASH_MANAGER'              => _('Trash'),
                'USER_GROUP_MANAGER'         => _('User groups'),
                'USER_MANAGER'               => _('Users'),
                'USERVOICE_SUPPORT'          => _('UserVoice integration'),
                'VIDEO_MANAGER'              => _('Videos'),
                'WIDGET_MANAGER'             => _('Widgets'),
                'PAYWALL'                    => _('Paywall'),
            );
        }

        return self::$availableModules;
    }

    /**
     * Returns an array with all available packs.
     *
     * @return array The array of packs.
     */
    public static function getAvailablePacks()
    {
        return [
            [
                'id'               => 'BASIC',
                'name'             => _('Basic pack'),
                'type'             => 'pack',
                'thumbnail'        => 'pack-basic.jpg',
                'description'      => _('Publishing your news is FREE!'),
                'long_description' => (
                    _(
                        '<p>This pack does not require any payment information and it allows you to access our platform to test it as much as to publish your newspaper for free and on the spot!</p>'
                    )
                    ._(
                        '<p>Includes:</p>
                        <ul>
                        <li>Frontpage manager</li>
                        <li>Widget manager</li>
                        <li>Opinion articles manager</li>
                        <li>Comments manager</li>
                        <li>Images and files uploading</li>
                        <li>Utilities: Trash, Search Advanced...</li>'
                    ).'</ul>'
                    .sprintf(
                        _(
                            '<ul>
                            <li>%d x  User (1)</li>
                            <li>%s Storage (2)</li>
                            <li>%s Items (Articles, Opinions, Comments) (2)</li>
                            <li>%s page views/month (2)</li>
                            <li>Online Support (Tickets SLA %d business days)</li></ul>'
                        ),
                        1,
                        '500MB',
                        '50.000',
                        '50.000',
                        4
                    )
                    ._(
                        '<p><small>1. To add more users refer to <a href="http://help.opennemas.com/knowledgebase/articles/368173-precios-opennemas-licencias-de-usuario" target="_blank">User Licence Page</a>.</small></p>'
                        .'<p><small>2. For more information about storage or page views please go to our page <a href="http://help.opennemas.com/knowledgebase/articles/227476-precios-opennemas-page-views-y-espacio-ocupado" target="_blank">Page Views and Storage Space</a>.</small></p>'
                        .'<p><small>All prices above do not include VAT (21%).</small></p>'
                    )
                ),
                'type' => 'pack',
                'price' => [
                    'month' => 0
                ]
            ],
            [
                'id'               => 'PROFESSIONAL',
                'name'             => _('Professional pack'),
                'type'             => 'pack',
                'thumbnail'        => 'pack-pro.jpg',
                'description'      => _('Our best selling solution, it allows to manage a professional newspaper and start gaining money with it!'),
                'long_description' => (
                    _(
                        '<p>This pack is thought for professional that are starting their indipendent newspaper and need to manage advertising to grow and to publish polls to engage with their audience.</p>
                        <p>This offer gives you more than 40% discount (if purchased separately modules have a value of 85EUR/month).</p>'
                    )
                    ._(
                        '<p>Includes:</p>
                        <ul>
                        <li>Frontpage manager</li>
                        <li>Widget manager</li>
                        <li>Opinion articles manager</li>
                        <li>Comments manager</li>
                        <li>Images and files uploading</li>
                        <li>Utilities: Trash, Search Advanced...</li>'
                    )
                    ._(
                        '<li>Advertisement manager</li>
                        <li>Polls manager</li>
                        <li>Galleries manager</li>
                        <li>Video manager</li>'
                    ).'</ul>'
                    .sprintf(
                        _(
                            '<ul>
                            <li>%d x  User (1)</li>
                            <li>%s Storage (2)</li>
                            <li>%s Items (Articles, Opinions, Comments) (2)</li>
                            <li>%s page views/month (2)</li>
                            <li>Online Support (Tickets SLA %d business days)</li></ul>'
                        ),
                        1,
                        '500MB',
                        '50.000',
                        '50.000',
                        2
                    )
                    ._(
                        '<p><small>1. To add more users refer to <a href="http://help.opennemas.com/knowledgebase/articles/368173-precios-opennemas-licencias-de-usuario" target="_blank">User Licence Page</a>.</small></p>'
                        .'<p><small>2. For more information about storage or page views please go to our page <a href="http://help.opennemas.com/knowledgebase/articles/227476-precios-opennemas-page-views-y-espacio-ocupado" target="_blank">Page Views and Storage Space</a>.</small></p>'
                        .'<p><small>All prices above do not include VAT (21%).</small></p>'
                    )
                ),
                'type'             => 'pack',
                'price' => [
                    'month' => 50
                ]
            ],
            [
                'id'               => 'ADVANCED',
                'type'             => 'pack',
                'thumbnail'        => 'pack-advanced.jpg',
                'name'             => _('Advanced pack'),
                'description'      => _('Personalize your frontpages and start sending newsletters to your readers and let them know what they have missed!.'),
                'long_description' => (
                    _(
                        '<p>A step further into the engagement path we add in this pack the possibility of sending newsletters and customize frontpage anytime you want.</p>
                        <p>This offer gives you more than 30% discount on modules (if purchased separately modules have a value of 145EUR/month).</p>'
                    )
                    ._(
                        '<p>Includes:</p>
                        <ul>
                        <li>Frontpage manager</li>
                        <li>Widget manager</li>
                        <li>Opinion articles manager</li>
                        <li>Comments manager</li>
                        <li>Images and files uploading</li>
                        <li>Utilities: Trash, Search Advanced...</li>'
                    )
                    ._(
                        '<li>Advertisement manager</li>
                        <li>Polls manager</li>
                        <li>Galleries manager</li>
                        <li>Video manager</li>'
                    )
                    ._(
                        '<li>Frontpage customization</li>
                        <li>Newsletter manager (<a href="http://opennemas.com/pricing?language=en_US#newsletter" target="_blank">0</a>)</li>'
                    ).'</ul>'
                    .sprintf(
                        _(
                            '<ul>
                            <li>%d x  User (1)</li>
                            <li>%s Storage (2)</li>
                            <li>%s Items (Articles, Opinions, Comments) (2)</li>
                            <li>%s page views/month (2)</li>
                            <li>Online Support (Tickets SLA %d business days)</li></ul>'
                        ),
                        1,
                        '500MB',
                        '50.000',
                        '50.000',
                        2
                    )
                    ._(
                        '<p><small>1. To add more users refer to <a href="http://help.opennemas.com/knowledgebase/articles/368173-precios-opennemas-licencias-de-usuario" target="_blank">User Licence Page</a>.</small></p>'
                        .'<p><small>2. For more information about storage or page views please go to our page <a href="http://help.opennemas.com/knowledgebase/articles/227476-precios-opennemas-page-views-y-espacio-ocupado" target="_blank">Page Views and Storage Space</a>.</small></p>'
                        .'<p><small>All prices above do not include VAT (21%).</small></p>'
                    )
                ),
                'price' => [
                    'month' => 100
                ]
            ],
            [
                'id'               => 'EXPERT',
                'name'             => _('Expert pack'),
                'type'             => 'pack',
                'thumbnail'        => 'pack-expert.jpg',
                'description'      => _('Add news from your favourites agencies, manage multiple personalized frontpages and let your readers become contributors!'),
                'long_description' => (
                    _('Add news from your favourites agencies, manage multiple personalized frontpages and let your readers to become contributors to your newspaper!')
                    ._(
                        '<p>This offer gives you more than 25% discount on modules (if purchased separately modules have a value of 260EUR/month). </p>'
                    )
                    ._(
                        '<p>Includes:</p>
                        <ul>
                        <li>Frontpage manager</li>
                        <li>Widget manager</li>
                        <li>Opinion articles manager</li>
                        <li>Comments manager</li>
                        <li>Images and files uploading</li>
                        <li>Utilities: Trash, Search Advanced...</li>'
                    )
                    ._(
                        '<li>Advertisement manager</li>
                        <li>Polls manager</li>
                        <li>Galleries manager</li>
                        <li>Video manager</li>'
                    )
                    ._(
                        '<li>Frontpage customization</li>
                        <li>Newsletter manager (<a href="http://opennemas.com/pricing?language=en_US#newsletter" target="_blank">0</a>)</li>'
                    )
                    ._(
                        '<li>Multiple Frontpage Management</li>
                        <li>News Agency</li>
                        <li>Opennemas Connect</li>'
                    ).'</ul>'
                    .sprintf(
                        _(
                            '<ul>
                            <li>%d x  User (1)</li>
                            <li>%s Storage (2)</li>
                            <li>%s Items (Articles, Opinions, Comments) (2)</li>
                            <li>%s page views/month (2)</li>
                            <li>Online Support (Tickets SLA %d business days)</li></ul>'
                        ),
                        1,
                        '500MB',
                        '50.000',
                        '50.000',
                        2
                    )
                    ._(
                        '<p><small>1. To add more users refer to <a href="http://help.opennemas.com/knowledgebase/articles/368173-precios-opennemas-licencias-de-usuario" target="_blank">User Licence Page</a>.</small></p>'
                        .'<p><small>2. For more information about storage or page views please go to our page <a href="http://help.opennemas.com/knowledgebase/articles/227476-precios-opennemas-page-views-y-espacio-ocupado" target="_blank">Page Views and Storage Space</a>.</small></p>'
                        .'<p><small>All prices above do not include VAT (21%).</small></p>'
                    )
                ),
                'type'  => 'pack',
                'price' => [
                    'month' => 200
                ]
            ]
        ];
    }

    /**
     * Returns an array with all available themes for market.
     *
     * @return array The array of themes for market.
     */
    public static function getAvailableThemes()
    {
        return [
            [
                'id'               => 'FREE_TEMPLATE',
                'type'             => 'theme',
                'thumbnail'        => 'theme-basic.jpg',
                'name'             => _('Free Basic Template'),
                'description'      => _('Change your site design with our free available templates.'),
                'long_description' => _(
                    '<ul>
                        <li>
                            Widgets: No widgets included. To add a widget please contact us at
                            <a href="mailto:sales@openhost.es">sales@openhost.es</a>
                        </li>
                        <li>Exclusivity: This template is not exclusive</li>
                        <li>Delivery time: Inmediate</li>
                    </ul>'
                ),
                'price' => [
                    'month' => 0
                ]
            ],
            // [
            //     'id'               => 'STANDARD_TEMPLATE',
            //     'type'             => 'theme',
            //     'thumbnail'        => 'theme-standard.jpg',
            //     'name'             => _('Standard Template'),
            //     'description'      => _(
            //         'Standard newspaper web site design with prebuild widgets '
            //         .'developed by Opennemas team. No customization available'
            //     ),
            //     'long_description' => _(
            //         '<ul>
            //             <li>
            //                 Widgets: Standard widgets included. To add a widget please contact us at
            //                 <a href="mailto:sales@openhost.es">sales@openhost.es</a>
            //             </li>
            //             <li>Exclusivity: This template is not exclusive</li>
            //             <li>Delivery time: 1 week</li>
            //             <li>Change request BEFORE launch: No change included</li>
            //             <li>Change request AFTER launch: No change included</li>
            //             <li>Add on:
            //                 <ul>
            //                     <li>New widgets: 120€ each</li>
            //                 </ul>
            //             </li>
            //         </ul>'
            //     ),
            //     'price' => [
            //         'single' => 350
            //     ]
            // ],
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
                    'single' => 1450,
                    'month' => 130
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
                    'single' => 3500,
                    'month' => 350
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
                    'single' => 3500,
                    'month'  => 350
                ]
            ],
        ];
    }

    /**
     * Returns the list of available modules in Onm instance.
     *
     * @return array the list of available modules
     */
    public static function getAvailableModulesGrouped()
    {
        if (!isset(self::$availableModulesGrouped)) {
            self::$availableModulesGrouped = [
                [
                    'id'               => 'ADS_MANAGER',
                    'plan'             => 'PROFESSIONAL',
                    'thumbnail'        => 'module-ads.jpg',
                    'type'             => 'module',
                    'name'             => _('Advertisement'),
                    'description'      => _('Gain money with your Opennemas newspaper: manage your ads!'),
                    'long_description' => _('<p>Thanks to this module all Opennemas journals will be able to create, add and manage ads on any pages: Frontpage Home/Sections, Inner Articles, Opinions, Gallery, Media.</p>
                        <p>There are more than 15 types of ads.</p>'),
                    'price'            => [
                        'month' => 35
                    ]
                ],
                [
                    'id'               => 'ADVANCED_SEARCH',
                    'plan'             => 'BASIC',
                    'type'             => 'internal',
                    'name'             => _('Advanced search'),
                    'description'      => _('Allows searching for content directly inside the manager'),
                    'long_description' => null,
                ],
                [
                    'id'               => 'ALBUM_MANAGER',
                    'plan'             => 'PROFESSIONAL',
                    'thumbnail'        => 'module-albums.jpg',
                    'type'             => 'module',
                    'name'             => _('Albums'),
                    'description'      => _('Allow you to create photo galleries and use them in your site.'),
                    'long_description' => _('<p>Add Video and Image Galleries to your content.</p>
                        <p>This module will allow you to create Photo Galleries, add video from YouTube, Vimeo, Dailymotion, MarcaTV, etc</p>
                        <p>And the most interesting fact is that the video manager is the same as youtube one, perfect consistency and performance.</p>'),
                ],
                [
                    'id'               => 'ARTICLE_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-articles.jpg',
                    'name'             => _('Articles'),
                    'description'      => _('Create your article and publish it with SEO included.'),
                    'long_description' => _('<p>Publish articles including Title, Subtitle, Summary, Comment, Image, whenever you want and from wherever you want.</p>'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'ADVANCED_ARTICLE_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Advanced article options'),
                    'description'      => _('Module to allow the second article signature'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'ADVANCED_FRONTPAGE_MANAGER',
                    'plan'             => 'OTHER',
                    'thumbnail'        => 'module-frontpage-adv-custom.jpg',
                    'type'             => 'module',
                    'name'             => _('Frontpage Customization'),
                    'description'      => _('Change the look and feel of your frontpages every time you want.'),
                    'long_description' => _('<p>Changing frontpage is more and more frequent in order to disrupt with daily monotony.</p>
                        <p>By activating this module you will be allowed to:</p>
                        <ul>
                            <li>Change background color of the news, so that a column can gain a different style from others.</li>
                            <li>Change font size, so that you can give more weight to a title in the page.</li>
                            <li>Change colour of titles fonts, so that you can combine it with the different background.</li>
                            <li>Change of style: font, bold, italic, etc.</li>
                            <li>Change the disposition of the image with respect to the text (right, left, above/below of the title, etc.)</li>
                        </ul>'),
                    'price'            => [
                        'month' => 30
                    ]
                ],
                [
                    'id'               => 'BLOG_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'thumbnail'        => 'module-opinion.jpg',
                    'name'             => _('Authors Blog'),
                    'description'      => _('Would you like to give to your Opinion authors a Blog Space, this is the feature you were looking for.'),
                    'long_description' => _('<p>Authors will have a dedicated space where all their contributions will appear in chronological order and readers alse set a RSS notification to receive the latest articles posted by a particular Author.</p>'),
                    'price'            => [
                        'month' => 20
                    ]
                ],
                [
                    'id'               => 'BOOK_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Books'),
                    'description'      => _('Module for managing book pages'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 'xxx'
                    ]
                ],
                [
                    'id'               => 'CACHE_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Cache manager'),
                    'description'      => _('Module for managing the cache of pages'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'CATEGORY_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-categories.jpg',
                    'name'             => _('Category'),
                    'description'      => _('Create, update and delete categories for contents.'),
                    'long_description' => _('<p>Module included in the Basic pack (FREE) of opennemas, it allows users to create and manage categories of content.</p>'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'COMMENT_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-comments.jpg',
                    'name'             => _('Comments'),
                    'description'      => _('Allow your readers to comment articles whether through Opennemas comment system or connecting with Facebook or Disqus.'),
                    'long_description' => _('<p>Module included in the FREE version, it allows users to add comments to any content generated by newspaper.'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'CRONICAS_MODULES',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Cronicas customizations'),
                    'description'      => _('Module for managing Cronicas customizations'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'FILE_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-files.jpg',
                    'name'             => _('Files'),
                    'description'      => _('Upload your files and share them on your newspaper.'),
                    'long_description' => _('<p>Module included in the Basic pack (FREE) of Opennemas, it allows users to upload files to the system and share them with a url in the newspaper articles.'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'FORM_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'thumbnail'        => 'module-letters.jpg',
                    'name'             => _('Contact'),
                    'description'      => _('Let your readers send you content with or without attachments.'),
                    'long_description' => _('<p>Our feature Opennemas Contact will allow readers to submit their opinions with or without attachment, so that they can communicate with the newspapers.</p>'),
                    'price'            => [
                        'month' => 25
                    ]
                ],
                [
                    'id'               => 'FRONTPAGE_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-frontpages.jpg',
                    'name'             => _('Frontpages'),
                    'description'      => _('Most important asset of a newspaper, the frontpage manager allows live update of frontpages content.'),
                    'long_description' => _('<p>Module included in the Basic pack (FREE) of opennemas, the frontpage manager will allow you to add articles and modify visualization of your frontpages instantly.<p>'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'FRONTPAGES_LAYOUT',
                    'plan'             => 'ADVANCED',
                    'type'             => 'module',
                    'thumbnail'        => 'module-frontpage-layouts.jpg',
                    'name'             => _('Frontpage Manager'),
                    'description'      => _('Create and Manage Frontpage Layouts made by you, every time you want!'),
                    'long_description' => _('<p>Manual Frontpages Disposition Management/Frontpages Organization</p>
                        <p>You can change the appearance of Opennemas newspaper in a matter of seconds with this module. In this way you can select a different frontpage model on each of the sections frontpages.</p>'),
                    'price'            => [
                        'month' => 45
                    ]
                ],
                [
                    'id'               => 'IMAGE_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-multimedia.jpg',
                    'name'             => _('Images'),
                    'description'      => _('Allows user to upload images'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'IADBOX_MANAGER',
                    'type'             => 'partner',
                    'author'           => '<a target="_blank" href="http://www.iadbox.com">iadbox</a>',
                    'plan'             => 'OTHER',
                    'thumbnail'        => 'iadbox.jpg',
                    'name'             => _('iadbox'),
                    'description'      => _('<p>iadbox is a way to serve ads when users want to receive them.</p>
                        <p>Let us know if you want to try it on your newspaper, we are alreday using it on ours!</p>
                        <p>We will set it up for you for FREE and let iadbox team know so that you receive your reports and revenue.</p>
                    '),
                    'long_description' => _('<p>iadbox is an intelligent commercial messaging for the smartphone generation.</p>
                        <p>It is a mobile and desktop marketing platform, with a user-controlled inbox for interaction with audiences.</p>
                        <p><strong>More info</strong>: <a target="_blank" href="http://www.iadbox.com">www.iadbox.com</a></p>
                        <p><a class="btn btn-success" href="mailto:sales@openhost.es">Ask for it</a></p>'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'XML_IMPORT',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'name'             => _('Import XMLs'),
                    'thumbnail'        => 'module-frontpage-adv-custom.jpg',
                    'description'      => _('Do you have your articles in Adobe InDesign or QuarkXPress'),
                    'long_description' => _('<p>By activating this module you will be able to import XML from Adobe InDesign and QuarkXPress, so that your print articles will become digital. This automatisation is one of the most popular because it saves so much time...</p>'),
                    'price'            => [
                        'month' => 75
                    ]
                ],
                [
                    'id'               => 'KEYWORD_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'name'             => _('Keywords'),
                    'thumbnail'        => 'module-tags.jpg',
                    'description'      => _('Create a list of your favourite keywords and choose if you want to link it to something or assign an action to it.'),
                    'long_description' => _('<p>Feature included in the Basic pack (FREE) of opennemas.</p>
                        <p>It allows you to create a list of keywords and assign a landing page to each of them, or mail_to, and automatically every time that this keyword will be used the system will tag it for you.</p>'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'KIOSKO_MANAGER',
                    'plan'             => 'EXPERT',
                    'type'             => 'module',
                    'thumbnail'        => 'module-newsstand.jpg',
                    'name'             => _('NewsStand'),
                    'description'      => _('Let your readers download the pdf copy of your print newspaper.'),
                    'long_description' => _('<p>If you would like to keep the print version of your newspaper in a newsstand like <a href="http://kiosko.net/" target="_blank">kiosko.net</a>, you just need to upload the full version or the frontpage and your users will be able to download it whenever they want.</p>'),
                    'price'            => [
                        'month' => 40
                    ]
                ],
                [
                    'id'               => 'LETTER_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'thumbnail'        => 'module-letters.jpg',
                    'name'             => _('Connect'),
                    'description'      => _('Let your readers be contributors.'),
                    'long_description' => _('<p>Our feature Opennemas Connect will allow readers to submit their news, so that the newspaper can become the "voice of people/Internet".</p>
                        <p>You will be able to create custom submission forms for your contributors so that they can share daily in the easiest way.</p>
                        <p>All contributions can be moderated.</p>'),
                    'price'            => [
                        'month' => '25'
                    ]
                ],
                [
                    'id'               => 'LIBRARY_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Library'),
                    'description'      => _('It allows to render list of contents in a page and classify it.'),
                    'long_description' => _('<p>Feature included in the Basic pack (FREE) of Opennemas in order to process in a page the list of the contents and classify it by section created on a given day.</p>'),
                    'price'            => [
                        'month' => '0'
                    ]
                ],
                [
                    'id'               => 'MENU_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-menus.jpg',
                    'name'             => _('Menus'),
                    'description'      => _('Control your site navegation with menus and custom elements.'),
                    'long_description' => _('<p>Feature included in the Basic Pack (FREE) of Opennemas, it enables you to control your site navigation.</p>
                        <p>You can add different kind of elements to menus:</p>
                        <ul>
                            <li>Internal links</li>
                            <li>Frontages</li>
                            <li>Static pages</li>
                            <li>External links</li>
                        </ul>'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'NEWS_AGENCY_IMPORTER',
                    'plan'             => 'EXPERT',
                    'type'             => 'module',
                    'thumbnail'        => 'module-agencies.jpg',
                    'name'             => _('News Agency importer'),
                    'description'      => _('Keeping your digital news up to date with agencies is already a reality!'),
                    'long_description' => _('<p>With just a few clicks the administrator will be able to add any news from agencies in the frontpage of the newspaper together with any image or media attached.</p>
                        <p>Whichever is your agency you can configure it: <a href="http://www.efe.com/" target="_blank">Agencia EFE</a>, <a href="http://www.europapress.es/" target="_blank">Agencia Europa press</a>, <a href="http://www.reuters.com/" target="_blank">Reuters</a> and other RSS/XML sources, etc. Every and each of this channels will be available in your administration panel.</p>'),
                    'price'            => [
                        'month' => 40
                    ]
                ],
                [
                    'id'               => 'NEWSLETTER_MANAGER',
                    'plan'             => 'ADVANCED',
                    'type'             => 'module',
                    'thumbnail'        => 'module-newsletters.jpg',
                    'name'             => _('Newsletter'),
                    'description'      => _('Engage your readers with your own personalised newsletter.'),
                    'long_description' => _('<p>It is more and more frequent that newspapers send bulletins with a selection of the most interesting news of the day/week/month.</p>
                        <p>This module allows administrators to create custom layouts of the bulletin and to edit the style before sending it.</p>
                        <p>This way you will be able to create the newsletter your style.</p>'),
                    'price'            => [
                        'month' => 30,
                        'usage' => [
                            'price' => 2,
                            'items' => 1000,
                            'type' => _('emails')
                        ]
                    ]
                ],
                [
                    'id'               => 'OPENNEMAS_AGENCY',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'thumbnail'        => 'module-frontpage-sync.jpg',
                    'name'             => _('Articles Synchronization'),
                    'description'      => _('Update more newspapers at once by syncing articles.'),
                    'long_description' => _('<p>Similarly to the frontpage synchronisation feature you will be able to update multiple newspapers by adding or editing articles in the main one.</p>
                        <p>If you have many local newspapers you can import all the articles you want from the main one.</p>'),
                    'price'            => [
                        'month' => 55
                    ]
                ],
                [
                    'id'               => 'OPINION_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-opinion.jpg',
                    'name'             => _('Opinion'),
                    'description'      => _('Have your opinionist publishing on your newspaper.'),
                    'long_description' => _('<p>Module included in the Basic pack (FREE) of Opennemas.</p>
                        <p>It allows all newspapers to have a section dedicated to opinions organized by author and with author frontpage too.</p>'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'POLL_MANAGER',
                    'plan'             => 'PROFESSIONAL',
                    'type'             => 'module',
                    'thumbnail'        => 'module-polls.jpg',
                    'name'             => _('Polls Manager'),
                    'description'      => _('Create and manage your polls, engage your audience and collect useful information.'),
                    'long_description' => _('<p>Polls questions can be created with single or multiple choice answers and the results displayed on bar charts, pie charts, etc.</p>
                        <p>The most relevant aspect is that they are compatible and available on any browsers in the world.</p>'),
                    'price'            => [
                        'month' => 15
                    ]
                ],
                [
                    'id'               => 'PROMOTIONAL_BAR',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Promotional bar'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'SCHEDULE_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Schedules'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'SETTINGS_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'internal',
                    'name'             => _('System wide settings'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'SPECIAL_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Specials'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 'xxx'
                    ]
                ],
                [
                    'id'               => 'STATIC_LIBRARY',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Static library'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 'xxx'
                    ]
                ],
                [
                    'id'               => 'STATIC_PAGES_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-static-page.jpg',
                    'name'             => _('Static pages'),
                    'description'      => _('Manage your internal information in a static page.'),
                    'long_description' => _('<p>Module included in the Basic pack (FREE) of opennemas.</p>
                        <p>It allows newspapers to have static pages if needed for legal information and or "about us" information.'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'SYNC_MANAGER',
                    'plan'             => 'ADVANCED',
                    'type'             => 'module',
                    'thumbnail'        => 'module-frontpage-sync.jpg',
                    'name'             => _('Frontpage Synchronization'),
                    'description'      => _('Update many frontpages by updating 1 frontpage!'),
                    'long_description' => _('<p>Do you have more than 1 newspaper and you would like for the "home" pages to be synchronised?</p>
                        <p>No problem. By activating this module you will have all your news synchronised in many frontpages all at once. For instance if you have many locak newspapers and one main one, you can update the frontpage of all locals with news of the general newspaper.</p>
                        <p>If you modify a frontpage in the main newspaper the frontpage of local newspapers will update automatically too.</p>
                        <p>The only requirement is that all newspapers need to belong to the same group, so that the frontpage is stored in the one place.</p>'),
                    'price'            => [
                        'month' => 65
                    ]
                ],
                [
                    'id'               => 'CONTENT_SUBSCRIPTIONS',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'thumbnail'        => 'module-paywall.jpg',
                    'name'             => _("Subscription"),
                    'description'      => _("<p>Would you like for your readers to be able to unlock premium content for free?</p><p>This is the module you should add to your newspaper!</p>"),
                    'long_description' => _('<p>This module enables newspapers to add a customized subscription form to allow users to unlock premium content.</p>
                        <p>The subscription form generates login details for the user.</p>
                        <p>Newspapers will be able to mark content as "premium" and users will need to register in order to access the full extent of it.</p>
                        <p>Note: if the newspaper needs to collect user\'s data will have to provide certification of inscription to "Agencia Española de Protección de Datos" or equivalent. Openhost, S.L. is not responsible of any data saved or required by newspapers.'),
                    'price'            => [
                        'month' => 65
                    ]
                ],
                [
                    'id'               => 'TRASH_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'internal',
                    'name'             => _('Trash'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'USER_GROUP_MANAGER',
                    'plan'             => 'ADVANCED',
                    'type'             => 'internal',
                    'name'             => _('User groups'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'USER_MANAGER',
                    'plan'             => 'ADVANCED',
                    'type'             => 'internal',
                    'name'             => _('Users'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'USERVOICE_SUPPORT',
                    'plan'             => 'BASIC',
                    'type'             => 'internal',
                    'name'             => _('UserVoice integration'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'VIDEO_MANAGER',
                    'plan'             => 'PROFESSIONAL',
                    'type'             => 'module',
                    'name'             => _('Videos'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'WIDGET_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'internal',
                    'name'             => _('Widgets'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'PAYWALL',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'thumbnail'        => 'module-paywall.jpg',
                    'name'             => _('Paywall'),
                    'description'      => _('Make money creating exclusive content only subscribers can access.'),
                    'long_description' => _('<p>The News business is a very challenging and advertising alone often does not allow to newspapers to keep going. Add paywall to your newspaper and you will be able to select articles that you want to sell. This way in order to access this news users will need to register.</p>
                        <p>You will be able to set the payment/subscription the way you want (weekly, monthly, annual, etc.) and also add currency and % of taxes that the item is subject to.</p>'),
                    'price'            => [
                        'month' => 95
                    ]
                ],
                [
                    'id'               => 'SUPPORT_NONE',
                    'plan'             => 'Support',
                    'type'             => 'internal',
                    'name'             => _('No support'),
                    'description'      => '',
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'SUPPORT_TRAINING',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-2.jpg',
                    'name'             => _('Training and Advisory Services'),
                    'description'      => _('Ask all your questions, walk through Opennemas and/or receive personal training via phone'),
                    'long_description' => _('<p>Do you need 2 hours on hangouts/skype/phone to ask all your questions or to walk through Opennemas and make sure you know it all?</p>
                        <p>This is the Support Offer perfect for you!</p>
                        <p>For a very small fee you will get our expert team on the line and you will be able to ask all the questions you have and/or receive personal training.</p>
                        <p>Please remember that we guarantee FREE support via tickets/emails.</p>'),
                    'price'            => [
                        'month' => 30
                    ]
                ],
                [
                    'id'               => 'SUPPORT_PRO',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-1.jpg',
                    'name'             => _('Support Pro'),
                    'description'      => _('This support plan is thought for changes and creation of new widgets.'),
                    'long_description' => _('<p>10 hours (2h day/1week).</p>
                        <p>Examples of usage of support:</p>
                        <ul>
                            <li>Creation and change of widgets</li>
                        </ul>
                        <p>Support provided by emails/tickets</p>'),
                    'price'            => [
                        'month' => 100
                    ]
                ],
                [
                    'id'               => 'SUPPORT_2',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-2.jpg',
                    'name'             => _('Support 2'),
                    'description'      => _('This support plan is ideal for updating your theme if you have had it for a long time.'),
                    'long_description' => _('<p>40 hours (2h per day during 1 month)</p>
                        <p>Examples of usage of support:</p>
                        <ul>
                            <li>Standard changes of HTML/CSS in templates</li>
                        </ul>
                        <p>Support provided by emails/tickets and hangouts/skype</p>'),
                    'price'            => [
                        'month' => 300
                    ]
                ],

                [
                    'id'               => 'SUPPORT_3',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-3.jpg',
                    'name'             => _('Support 3'),
                    'description'      => _('This support plan is ideal for updating your theme and at the same time redefining spaces.'),
                    'long_description' => _('<p>60 hours (3h day/1month)</p>
                        <p>Examples of usage of support:</p>
                        <ul>
                            <li>Standard changes of HTML/CSS in templates</li>
                        </ul>
                        <p>Support provided by emails/tickets and hangouts/skype</p>'),
                    'price'            => [
                        'month' => 450
                    ]
                ],
                [
                    'id'               => 'SUPPORT_4',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-4.jpg',
                    'name'             => _('Support 4'),
                    'description'      => _('This support plan is ideal for updating your theme and at the same time redefining spaces.'),
                    'long_description' => _('<p>80 hours (4h day/1month)</p>
                        <p>Examples of usage of support:</p>
                        <ul>
                            <li>Examples of usage of support:
                            <li>Standard changes of HTML/CSS in templates</li>
                            <li>Change Requests of the disposition of frontpage templates</li>
                            <li>Category Titles, New structure of inner articles.</li>
                        </ul>
                        <p>Support provided by emails/tickets and hangouts/skype</p>'),
                    'price'            => [
                        'month' => 600
                    ]
                ],
                [
                    'id'               => 'SUPPORT_8',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-8.jpg',
                    'name'             => _('Support 8'),
                    'description'      => _('This support plan fits the purpose of a restyling of newspapers by redefining spaces, disegn and style.'),
                    'long_description' => _('<p>160 hours (8h day/1month)</p>
                        <p>This support is all about customization and having one of our resources dedicated to a newsaper full time.</p>
                        <p>Examples of usage of support:</p>
                        <ul>
                            <li>Creation and change requests of widgets
                            <li>Standard changes of HTML/CSS in templates</li>
                            <li>Change Requests of the disposition of frontpage templates</li>
                            <li>Category Titles</li>
                            <li>New structure of inner articles.</li>
                        </ul>
                        <p>Support provided by emails/tickets, hangouts/skype and phone</p>'),
                    'price'            => [
                        'month' => 1200
                    ]
                ],
                [
                    'id'               => 'SUPPORT_8_PLUS',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-8plus.jpg',
                    'name'             => _('Support 8 Plus'),
                    'description'      => _('This Support is designed for all newspapers that need may need help including during the weekend.'),
                    'long_description' => _('<p>8h day/7 days/ 1month</p>
                        <p>Examples of usage of support:</p>
                        <ul>
                            <li>Creation and change requests of widgets</li>
                            <li>Standard changes of HTML/CSS in templates</li>
                            <li>Change Requests of the disposition of frontpage templates</li>
                            <li>Category Titles</li>
                            <li>New structure of inner articles.</li>
                        </ul>
                        <p>Support provided by emails/tickets, hangouts/skype and phone</p>'),
                    'price'            => [
                        'month' => 3000
                    ]
                ]
            ];
        }

        return self::$availableModulesGrouped;
    }

    /**
     * Returns the list of internal modules names
     *
     * @return array the list of internal names
     **/
    public static function getAvailableModuleNames()
    {
        $modules = self::getAvailableModules();

        return array_keys($modules);
    }

    /**
     * Returns if all modules are activated.
     *
     * @return boolean true if all modules are activated
     */
    public static function checkAllModulesActivated()
    {
        $activatedModules = getService('instance_manager')->current_instance
            ->activated_modules;

        if (!isset($activatedModules) or !is_array($activatedModules)) {
            return true;
        }

        return in_array('ALL', $activatedModules);
    }

    /**
     * Returns true if a given module is activated.
     *
     * @param string $module the module canonical name.
     *
     * @return boolean true if module is activated, otherwise false
     *
     * @throws <b>ModuleException</b> If module is not available.
     */
    public static function isActivated($module = '')
    {
        if (Acl::isMaster()) {
            return true;
        }

        if (!isset($module) || empty($module)) {
            // Check if module name is properly setted

            return true;
        } elseif (self::checkAllModulesActivated()) {
            // Check if all modules are activated

            return true;
        } elseif (!self::moduleExists($module)) {
            // Check if module exists

            return false; // Hack to avoid crashes
            throw new ModuleException("Module '{$module} is not available");
        } else {
            // Finally return if that module is activated
            return in_array($module, self::getActivatedModules());
        }
    }

    /**
     * Returns true if a given module is activated or
     * forward  if is not activated
     *
     * @param string $module the module canonical name.
     *
     * @return boolean true if module is activated, otherwise false
     *
     * @throws <b>ModuleException</b> If module is not available.
     */
    public static function checkActivatedOrForward($module = '')
    {
        try {
            // Check if module exists
            if (self::isActivated($module)) {
                return true;
            }
        } catch (ModuleException $e) {
            $_SESSION['error'] = $e->getMessage();
            throw new AccessDeniedException();
        }

    }

    /**
     * Check if a given module exists.
     *
     * @param string $moduleName the name of the module to check.
     *
     * @return boolean true if module is in available modules list
     */
    public static function moduleExists($moduleName)
    {
        $moduleNames = self::getAvailableModuleNames();

        return in_array($moduleName, $moduleNames);
    }
}
