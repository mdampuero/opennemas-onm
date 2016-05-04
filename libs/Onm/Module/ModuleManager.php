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
                'FIA_MODULE'                 => _('Facebook Instant Articles integration'),
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
                'price' => []
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
                ],
                [
                    'id'               => 'ADVANCED_SEARCH',
                    'plan'             => 'BASIC',
                    'type'             => 'internal',
                    'name'             => _('Advanced search'),
                ],
                [
                    'id'               => 'ALBUM_MANAGER',
                    'plan'             => 'PROFESSIONAL',
                    'thumbnail'        => 'module-albums.jpg',
                    'type'             => 'module',
                    'name'             => _('Albums'),
                ],
                [
                    'id'               => 'ARTICLE_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-articles.jpg',
                    'name'             => _('Articles'),
                ],
                [
                    'id'               => 'ADVANCED_ARTICLE_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Advanced article options'),
                ],
                [
                    'id'               => 'ADVANCED_FRONTPAGE_MANAGER',
                    'plan'             => 'OTHER',
                    'thumbnail'        => 'module-frontpage-adv-custom.jpg',
                    'type'             => 'module',
                    'name'             => _('Frontpage Customization'),
                ],
                [
                    'id'               => 'BLOG_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'thumbnail'        => 'module-opinion.jpg',
                    'name'             => _('Authors Blog'),
                ],
                [
                    'id'               => 'BOOK_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Books'),
                ],
                [
                    'id'               => 'CACHE_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Cache manager'),
                ],
                [
                    'id'               => 'CATEGORY_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-categories.jpg',
                    'name'             => _('Category'),
                ],
                [
                    'id'               => 'COMMENT_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-comments.jpg',
                    'name'             => _('Comments'),
                ],
                [
                    'id'               => 'CRONICAS_MODULES',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Cronicas customizations'),
                ],
                [
                    'id'               => 'FILE_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-files.jpg',
                    'name'             => _('Files'),
                ],
                [
                    'id'               => 'FORM_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'thumbnail'        => 'module-letters.jpg',
                    'name'             => _('Contact'),
                ],
                [
                    'id'               => 'FRONTPAGE_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-frontpages.jpg',
                    'name'             => _('Frontpages'),
                ],
                [
                    'id'               => 'FRONTPAGES_LAYOUT',
                    'plan'             => 'ADVANCED',
                    'type'             => 'module',
                    'thumbnail'        => 'module-frontpage-layouts.jpg',
                    'name'             => _('Frontpage Manager'),
                ],
                [
                    'id'               => 'IMAGE_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-multimedia.jpg',
                    'name'             => _('Images'),
                ],
                [
                    'id'               => 'IADBOX_MANAGER',
                    'type'             => 'partner',
                    'author'           => '<a target="_blank" href="http://www.iadbox.com">iadbox</a>',
                    'plan'             => 'OTHER',
                    'thumbnail'        => 'iadbox.jpg',
                    'name'             => _('iadbox'),
                ],
                [
                    'id'               => 'XML_IMPORT',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'name'             => _('Import XMLs'),
                    'thumbnail'        => 'module-frontpage-adv-custom.jpg',
                ],
                [
                    'id'               => 'KEYWORD_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'name'             => _('Keywords'),
                    'thumbnail'        => 'module-tags.jpg',
                ],
                [
                    'id'               => 'KIOSKO_MANAGER',
                    'plan'             => 'EXPERT',
                    'type'             => 'module',
                    'thumbnail'        => 'module-newsstand.jpg',
                    'name'             => _('NewsStand'),
                ],
                [
                    'id'               => 'LETTER_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'thumbnail'        => 'module-letters.jpg',
                    'name'             => _('Connect'),
                ],
                [
                    'id'               => 'LIBRARY_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Library'),
                ],
                [
                    'id'               => 'MENU_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-menus.jpg',
                    'name'             => _('Menus'),
                ],
                [
                    'id'               => 'NEWS_AGENCY_IMPORTER',
                    'plan'             => 'EXPERT',
                    'type'             => 'module',
                    'thumbnail'        => 'module-agencies.jpg',
                    'name'             => _('News Agency importer'),
                ],
                [
                    'id'               => 'NEWSLETTER_MANAGER',
                    'plan'             => 'ADVANCED',
                    'type'             => 'module',
                    'thumbnail'        => 'module-newsletters.jpg',
                    'name'             => _('Newsletter'),
                    'description'      => _('Engage your readers with your own personalised newsletter.'),
                ],
                [
                    'id'               => 'OPENNEMAS_AGENCY',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'thumbnail'        => 'module-frontpage-sync.jpg',
                    'name'             => _('Articles Synchronization'),
                ],
                [
                    'id'               => 'OPINION_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-opinion.jpg',
                    'name'             => _('Opinion'),
                ],
                [
                    'id'               => 'POLL_MANAGER',
                    'plan'             => 'PROFESSIONAL',
                    'type'             => 'module',
                    'thumbnail'        => 'module-polls.jpg',
                    'name'             => _('Polls Manager'),
                ],
                [
                    'id'               => 'PROMOTIONAL_BAR',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Promotional bar'),
                ],
                [
                    'id'               => 'SCHEDULE_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Schedules'),
                ],
                [
                    'id'               => 'SETTINGS_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'internal',
                    'name'             => _('System wide settings'),
                ],
                [
                    'id'               => 'SPECIAL_MANAGER',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Specials'),
                ],
                [
                    'id'               => 'STATIC_LIBRARY',
                    'plan'             => 'OTHER',
                    'type'             => 'internal',
                    'name'             => _('Static library'),
                ],
                [
                    'id'               => 'STATIC_PAGES_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'module',
                    'thumbnail'        => 'module-static-page.jpg',
                    'name'             => _('Static pages'),
                ],
                [
                    'id'               => 'SYNC_MANAGER',
                    'plan'             => 'ADVANCED',
                    'type'             => 'module',
                    'thumbnail'        => 'module-frontpage-sync.jpg',
                    'name'             => _('Frontpage Synchronization'),
                ],
                [
                    'id'               => 'CONTENT_SUBSCRIPTIONS',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'thumbnail'        => 'module-paywall.jpg',
                    'name'             => _("Subscription"),
                ],
                [
                    'id'               => 'TRASH_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'internal',
                    'name'             => _('Trash'),
                ],
                [
                    'id'               => 'USER_GROUP_MANAGER',
                    'plan'             => 'ADVANCED',
                    'type'             => 'internal',
                    'name'             => _('User groups'),
                ],
                [
                    'id'               => 'USER_MANAGER',
                    'plan'             => 'ADVANCED',
                    'type'             => 'internal',
                    'name'             => _('Users'),
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
                ],
                [
                    'id'               => 'WIDGET_MANAGER',
                    'plan'             => 'BASIC',
                    'type'             => 'internal',
                    'name'             => _('Widgets'),
                ],
                [
                    'id'               => 'PAYWALL',
                    'plan'             => 'OTHER',
                    'type'             => 'module',
                    'thumbnail'        => 'module-paywall.jpg',
                    'name'             => _('Paywall'),
                ],
                [
                    'id'               => 'SUPPORT_NONE',
                    'plan'             => 'Support',
                    'type'             => 'internal',
                    'name'             => _('No support'),
                ],
                [
                    'id'               => 'SUPPORT_TRAINING',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-2.jpg',
                    'name'             => _('Training and Advisory Services'),
                ],
                [
                    'id'               => 'SUPPORT_PRO',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-1.jpg',
                    'name'             => _('Support Pro'),
                ],
                [
                    'id'               => 'SUPPORT_2',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-2.jpg',
                    'name'             => _('Support 2'),
                ],

                [
                    'id'               => 'SUPPORT_3',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-3.jpg',
                    'name'             => _('Support 3'),
                ],
                [
                    'id'               => 'SUPPORT_4',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-4.jpg',
                    'name'             => _('Support 4'),
                ],
                [
                    'id'               => 'SUPPORT_8',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-8.jpg',
                    'name'             => _('Support 8'),
                ],
                [
                    'id'               => 'SUPPORT_8_PLUS',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'thumbnail'        => 'service-8plus.jpg',
                    'name'             => _('Support 8 Plus'),
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
