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
     * Returns the list of available modules in Onm instance.
     *
     * @return array the list of available modules
     */
    public static function getAvailableModules()
    {
        if (!isset(self::$availableModules)) {
            self::$availableModules = array(
                'ADS_MANAGER'               => _('Advertisement'),
                'ADVANCED_SEARCH'           => _('Advanced search'),
                'ALBUM_MANAGER'             => _('Albums'),
                'ARTICLE_MANAGER'           => _('Articles'),
                'AVANCED_ARTICLE_MANAGER'   => _('Advanced article options'),
                'AVANCED_FRONTPAGE_MANAGER' => _('Advanced frontpage managers'),
                'BLOG_MANAGER'              => _('Blog'),
                'BOOK_MANAGER'              => _('Books'),
                'CACHE_MANAGER'             => _('Cache manager'),
                'CATEGORY_MANAGER'          => _('Category'),
                'COMMENT_MANAGER'           => _('Comments'),
                'CRONICAS_MODULES'          => _('Cronicas customizations'),
                'FILE_MANAGER'              => _('Files'),
                'FORM_MANAGER'              => _('Forms'),
                'FRONTPAGE_MANAGER'         => _('Frontpages'),
                'FRONTPAGES_LAYOUT'         => _('Frontpages layout'),
                'IMAGE_MANAGER'             => _('Images'),
                'KEYWORD_MANAGER'           => _('Keywords'),
                'KIOSKO_MANAGER'            => _('Kiosko'),
                'LETTER_MANAGER'            => _('Letters'),
                'LIBRARY_MANAGER'           => _('Library'),
                'MENU_MANAGER'              => _('Menus'),
                'NEWS_AGENCY_IMPORTER'      => _('News Agency importer'),
                'NEWSLETTER_MANAGER'        => _('Newsletter'),
                'OPINION_MANAGER'           => _('Opinion'),
                'POLL_MANAGER'              => _('Polls'),
                'PROMOTIONAL_BAR'           => _('Promotional bar'),
                'SCHEDULE_MANAGER'          => _('Schedules'),
                'SETTINGS_MANAGER'          => _('System wide settings'),
                'SPECIAL_MANAGER'           => _('Specials'),
                'STATIC_LIBRARY'            => _('Static library'),
                'STATIC_PAGES_MANAGER'      => _('Static pages'),
                'SUPPORT_NONE'              => _('Basic support'),
                'SUPPORT_PRO'               => _('Profesional support'),
                'SUPPORT_2'                 => _('Support 2'),
                'SUPPORT_4'                 => _('Support 4'),
                'SUPPORT_8'                 => _('Support 8'),
                'SUPPORT_8_PLUS'            => _('Support 8+'),
                'SYNC_MANAGER'              => _('Instance synchronization'),
                'TRASH_MANAGER'             => _('Trash'),
                'USER_GROUP_MANAGER'        => _('User groups'),
                'USER_MANAGER'              => _('Users'),
                'USERVOICE_SUPPORT'         => _('UserVoice integration'),
                'VIDEO_MANAGER'             => _('Videos'),
                'WIDGET_MANAGER'            => _('Widgets'),
                'PAYWALL'                   => _('Paywall'),
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
                'description'      => _('Features the basic functionality for your newspaper for free.'),
                'long_description' => _(
                    '<p>Publishing your news is <strong>FREE!</strong></p>
                    This pack includes:
                    <ul>
                        <li>Frontpage manager</li>
                        <li>Widget manager</li>
                        <li>Opinion articles manager</li>
                        <li>Comments manager</li>
                        <li>Images and files uploading</li>
                        <li>Utilities: Trash, Search Advanced...</li>
                        <li>Support via tickets</li>
                        <li>Media storage: 500MB</li>
                        <li>Page views: 50.000</li>
                    </ul>'
                ),
                'type'             => 'pack',
                'price' => [
                    'month' => 0
                ]
            ],
            [
                'id'               => 'PROFESSIONAL',
                'name'             => _('Professional pack'),
                'description'      => _('Our best selling solution, it allows to manage a professional newspaper and start gaining money with it!'),
                'long_description' => _(
                    '<p>This offer gives you more than 40% discount (if purchased separately modules
                    have a value of 85EUR/month)</p>
                    <p>This pack includes:</p>
                    <ul>
                        <li>Frontpage manager</li>
                        <li>Widget manager</li>
                        <li>Opinion articles manager</li>
                        <li>Comments manager</li>
                        <li>Images and files uploading</li>
                        <li>Utilities: Trash, Search Advanced...</li>
                        <li>Advertisement manager</li>
                        <li>Polls manager</li>
                        <li>Galleries manager</li>
                        <li>Video manager</li>
                        <li>1 user license</li>
                        <li>Support via tickets</li>
                        <li>Media storage: 1GB</li>
                        <li>Page views: 100.000</li>
                    </ul>'
                ),
                'type'             => 'pack',
                'price' => [
                    'month' => 50
                ]
            ],
            [
                'id'               => 'SILVER',
                'type'             => 'pack',
                'name'             => _('Advanced pack'),
                'description'      => _('Provides advanced features to personalize your site and add more kind of contents.'),
                'long_description' => _(
                    '<p>Personalize your frontpages and start sending newsletters
                    to your readers and let them know what they have missed!</p>
                    <p>This offer gives you more than 30% discount on modules (if purchased
                    separately modules have a value of 145EUR/month).</p>
                    This pack includes:
                    <ul>
                        <li>Frontpage manager</li>
                        <li>Widget manager</li>
                        <li>Opinion articles manager</li>
                        <li>Comments manager</li>
                        <li>Images and files uploading</li>
                        <li>Utilities: Trash, Search Advanced...</li>
                        <li>Advertisement manager</li>
                        <li>Polls manager</li>
                        <li>Galleries manager</li>
                        <li>Video manager</li>
                        <li>Frontpage customization</li>
                        <li>Newsletter manager (*)</li>
                        <li>2 user license</li>
                        <li>Support via tickets</li>
                        <li>Support via phone: 4h (10am-2pm M-F)</li>
                        <li>Media storage: 1.5GB</li>
                        <li>Page views: 250.000</li>
                    </ul>
                    <p><small>*  Newsletter manager: email sendings are charged with
                        0.3€ each block of 1000 sent emails</small></p>'
                ),
                'price' => [
                    'month' => 250
                ]
            ],
            [
                'id'               => 'GOLD',
                'name'             => _('Expert pack'),
                'description'      => _('Contains all the major features of Opennemas.'),
                'long_description' => _(
                    '<p>Personalize your frontpages and start sending newsletters
                    to your readers and let them know what they have missed!</p>
                    <p>This offer gives you more than 30% discount on modules (if purchased
                    separately modules have a value of 145EUR/month).</p>
                    This pack includes:
                    <ul>
                        <li>Frontpage manager</li>
                        <li>Widget manager</li>
                        <li>Opinion articles manager</li>
                        <li>Comments manager</li>
                        <li>Images and files uploading</li>
                        <li>Utilities: Trash, Search Advanced...</li>
                        <li>Advertisement manager</li>
                        <li>Polls manager</li>
                        <li>Galleries manager</li>
                        <li>Video manager</li>
                        <li>Frontpage customization</li>
                        <li>Newsletter manager (*)</li>
                        <li>5 user license</li>
                        <li>Support via tickets</li>
                        <li>Support via phone: 8h (10am-6pm M-F)</li>
                        <li>Media storage: 2.5GB</li>
                        <li>Page views: 500.000</li>
                    </ul>
                    <p><small>*  Newsletter manager: email sendings are charged with 0.3€
                    each block of 1000 sent emails</small></p>'
                ),
                'type'             => 'pack',
                'price' => [
                    'month' => 500
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
                'name'             => _('Free Basic Template'),
                'description'      => _('Change your site design with our free available templates.'),
                'long_description' => _(
                    '<ul>
                        <li>
                            Widgets: No widgets included. To add a widget please contact us at
                            <a href="mailto:sales@openhost.es">sales@openhost.es</a>
                        </li>
                        <li>Exclusivity: This template is not exclusive</li>
                        <li>Delivery time: On the spot</li>
                    </ul>'
                ),
                'price' => [
                    'month' => 0
                ]
            ],
            [
                'id'               => 'STANDARD_TEMPLATE',
                'type'             => 'theme',
                'name'             => _('Standard Template'),
                'description'      => _(
                    'Standard newspaper web site design with prebuild widgets '
                    .'developed by Opennemas team. No customization available'
                ),
                'long_description' => _(
                    '<ul>
                        <li>
                            Widgets: Standard widgets included. To add a widget please contact us at
                            <a href="mailto:sales@openhost.es">sales@openhost.es</a>
                        </li>
                        <li>Exclusivity: This template is not exclusive</li>
                        <li>Delivery time: 1 week</li>
                        <li>Change request BEFORE launch: No change included</li>
                        <li>Change request AFTER launch: No change included</li>
                        <li>Add on:
                            <ul>
                                <li>New widgets: 120€ each</li>
                            </ul>
                        </li>
                    </ul>'
                ),
                'price' => [
                    'single' => 350
                ]
            ],
            [
                'id'               => 'CUSTOM_TEMPLATE',
                'type'             => 'theme',
                'name'             => _('Custom Template'),
                'description'      => _(
                    'Newspaper web site template developed by Opennemas team.'
                    .' Customizable to make a better git to customer brand guidelines and image'
                ),
                'long_description' => _(
                    '<ul>
                        <li>
                            Widgets: Standard widgets included. To add a widget please contact us at
                            <a href="mailto:sales@openhost.es">sales@openhost.es</a>
                        </li>
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
                    'month' => 135
                ]
            ],
            [
                'id'               => 'EXCLUSIVE_TEMPLATE',
                'type'             => 'theme',
                'name'             => _('Exclusive Template'),
                'description'      => _(
                    'Newspaper Web Site Template that can be customized to reflect '
                    .'better brand guidelines and customer preferences'
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
                                    Up to 5 more widgets included
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
                                    <a href="http://help.opennemas.com/knowledgebase/articles/463594-precios-opennemas-servicio-de-desarrollo">
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
                'name'             => _('Custom Exclusive Template'),
                'description'      => _('Newspaper web site developed from scratch by Opennemas team.'),
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
                                    <a href="http://help.opennemas.com/knowledgebase/articles/463594-precios-opennemas-servicio-de-desarrollo">
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
                    'plan'             => 'Profesional',
                    'type'             => 'module',
                    'name'             => _('Advertisement'),
                    'description'      => _('Gain money by inserting ads in your site. Images, scripts and external services integration'),
                    'long_description' => _(
                        '<p>Thanks to this module all opennemas journals will be able to
                        create, add and manage ads on any pages:</p>
                        <ul>
                            <li>Frontpage Home/Sections</li>
                            <li>Inner Articles</li>
                            <li>Opinions</li>
                            <li>Gallery</li>
                            <li>Media</li>
                        </ul>
                        <p>There are more than 15 types of ads.</p>'
                    ),
                    'price'            => [
                        'month' => 35
                    ]
                ],
                [
                    'id'               => 'ADVANCED_SEARCH',
                    'plan'             => 'Base',
                    'type'             => 'internal',
                    'name'             => _('Advanced search'),
                    'description'      => _('Allows searching for content directly inside the manager'),
                    'long_description' => null,
                ],
                [
                    'id'               => 'ALBUM_MANAGER',
                    'plan'             => 'Profesional',
                    'type'             => 'module',
                    'name'             => _('Albums'),
                    'description'      => _('Allow you to create photo galleries and use them in your site.'),
                    'long_description' => _(
                        '<p>Add Video and Image Galleries to your content.</p>
                        <p>This module will allow you to create Photo Galleries, add video from YouTube,
                        Vimeo, Dailymotion, MarcaTV, etc</p>
                        <p>And the most interesting fact is that the video manager is the
                        same as youtube one, perfect consistency and performance.</p>'
                    ),
                ],
                [
                    'id'               => 'ARTICLE_MANAGER',
                    'plan'             => 'Base',
                    'type'             => 'module',
                    'name'             => _('Articles'),
                    'description'      => _('Module for managing articles'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'AVANCED_ARTICLE_MANAGER',
                    'plan'             => 'Other',
                    'type'             => 'internal',
                    'name'             => _('Advanced article options'),
                    'description'      => _('Module to allow the second article signature'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'AVANCED_FRONTPAGE_MANAGER',
                    'plan'             => 'Other',
                    'type'             => 'module',
                    'name'             => _('Frontpage advanced personalization'),
                    'description'      => _('Change your Frontpage every time you want.'),
                    'long_description' => _(
                        '
                        <p>Changing frontpage is more and more frequent in order to disrupt with daily monotony.</p>
                        <p>By activating this module you will be allowed to:</p>
                        <ul>
                            <li>Change background colour of the news, so that a column can gain a different style from others.</li>
                            <li>Change font size, so that you can give more weight to a title in the page.</li>
                            <li>Change colour of titles fonts, so that you can combine it with the different background.</li>
                            <li>Change of style: font, bold, italic, etc</li>
                            <li>Change the disposition of the image with respect to the text: right, left, above/below of the title, etc</li>
                        </ul>'
                    ),
                    'price'            => [
                        'month' => 30
                    ]
                ],
                [
                    'id'               => 'BLOG_MANAGER',
                    'plan'             => 'Other',
                    'type'             => 'internal',
                    'name'             => _('Blog'),
                    'description'      => _('Module to manage reviews with blog format'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'BOOK_MANAGER',
                    'plan'             => 'Other',
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
                    'plan'             => 'Other',
                    'type'             => 'internal',
                    'name'             => _('Cache manager'),
                    'description'      => _('Module for managing the cache of pages'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'CATEGORY_MANAGER',
                    'plan'             => 'Base',
                    'type'             => 'module',
                    'name'             => _('Category'),
                    'description'      => _('Module for managing categories'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'COMMENT_MANAGER',
                    'plan'             => 'Base',
                    'type'             => 'module',
                    'name'             => _('Comments'),
                    'description'      => _('Module for managing comments'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'CRONICAS_MODULES',
                    'plan'             => 'Other',
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
                    'plan'             => 'Base',
                    'type'             => 'module',
                    'name'             => _('Files'),
                    'description'      => _('Allows the user to upload files'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'FORM_MANAGER',
                    'plan'             => 'Other',
                    'type'             => 'internal',
                    'name'             => _('Opennemas Connect'),
                    'description'      => _('Allows to create to new custom forms'),
                    'long_description' => _(
                        '<p>Let your readers be contributors.</p>
                        <p>Our feature Opennemas Connect will allow readers to submit their news, so that the newspaper
                        can become the "voice of people/Internet".</p><p>You will be able to create custom submission
                        forms for your contributors so that they can share daily in the easiest way.</p>
                        <p>All contributions can be moderated.</p>'
                    ),
                    'price'            => [
                        'month' => 15
                    ]
                ],
                [
                    'id'               => 'FRONTPAGE_MANAGER',
                    'plan'             => 'Base',
                    'type'             => 'module',
                    'name'             => _('Frontpages'),
                    'description'      => _('Module for managing elements in frontpages'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'FRONTPAGES_LAYOUT',
                    'plan'             => 'Silver',
                    'type'             => 'module',
                    'name'             => _('Frontpages layouts & custom layouts'),
                    'description'      => _('Create and ManageFrontpageLayouts made by you, every time you want!'),
                    'long_description' => _(
                        '<p>This module has two distinct parts: Manual or Automatic Personal Frontpages Management.</p>

                        <h3>MANUAL</h3>
                        <p>Management Personal Frontpages/Frontpages Organization</p>
                        <p>You can change the appearance of Opennemas newspaper is a matter of seconds with this module.
                        In this way you can select a different frontpage model on each of the sections frontpages.</p>

                        <h3>AUTOMATIC</h3>
                        <p>If you do not want to have to create layouts, the system can do it for you.</p>'
                    ),
                    'price'            => [
                        'month' => 45
                    ]
                ],
                [
                    'id'               => 'IMAGE_MANAGER',
                    'plan'             => 'Base',
                    'type'             => 'module',
                    'name'             => _('Images'),
                    'description'      => _('Allows user to upload images'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'KEYWORD_MANAGER',
                    'plan'             => 'Base',
                    'type'             => 'module',
                    'name'             => _('Keywords'),
                    'description'      => _('Allows user to define keywords associated with url, mails and internal searches'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'KIOSKO_MANAGER',
                    'plan'             => 'Gold',
                    'type'             => 'module',
                    'name'             => _('Kiosko'),
                    'description'      => _('Let your readers download the pdf copy of your print newspaper.'),
                    'long_description' => _(
                        '<p>If you would like to keep the print version of your newspaper in a newsstand like
                        <a href="http://kiosko.net/" target="_blank">kiosko.net</a>, you just need to upload the full
                        version or the frontpage and your users will be able to download it whenever they want.</p>'
                    ),
                    'price'            => [
                        'month' => 40
                    ]
                ],
                [
                    'id'               => 'LETTER_MANAGER',
                    'plan'             => 'Other',
                    'name'             => _('Letters'),
                    'description'      => _('Allows user to publish letters sent to the director'),
                    'long_description' => _('Missed long description'),
                    'type'             => 'module',
                    'price'            => [
                        'month' => 'xxx'
                    ]
                ],
                [
                    'id'               => 'LIBRARY_MANAGER',
                    'plan'             => 'Other',
                    'type'             => 'internal',
                    'name'             => _('Library'),
                    'description'      => _('With this module users can access all contents by date'),
                    'long_description' => _('Missed long description'),
                    'price'            => [
                        'month' => 'xxx'
                    ]
                ],
                [
                    'id'               => 'MENU_MANAGER',
                    'plan'             => 'Base',
                    'type'             => 'module',
                    'name'             => _('Menus'),
                    'description'      => _('Control your site navegation with menus and custom elements.'),
                    'long_description' => _('<p>This module enables you to control your site navigation menus.</p>
                        <p>Add different kinds of elements into menus:</p>
                        <ul>
                            <li>Internal links</li>
                            <li>Frontages</li>
                            <li>Static pages</li>
                            <li>External links</li>
                            <li>...</li>
                        </ul>'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'NEWS_AGENCY_IMPORTER',
                    'plan'             => 'Gold',
                    'type'             => 'module',
                    'name'             => _('News Agency importer'),
                    'description'      => _('Keeping your digital news up to date with agencies is already a reality!'),
                    'long_description' => _(
                        '<p>Keeping your digital news up to date with agencies is already a reality!</p>
                        <p><a href="http://www.efe.com/" target="_blank">Agencia EFE</a>,
                        <a href="http://www.europapress.es/" target="_blank">agencia Europa press</a>,
                        <a href="http://www.reuters.com/" target="_blank">Reuters</a>, etc Every and each of this
                        channels will be available for opennemas newspapers. With just few clicks the administrator
                        will be able to add any news from agencies in the frontpage of the newspaper together with
                        any image or media attached.</p>'
                    ),
                    'price'            => [
                        'month' => 40
                    ]
                ],
                [
                    'id'               => 'NEWSLETTER_MANAGER',
                    'plan'             => 'Silver',
                    'type'             => 'module',
                    'name'             => _('Newsletter'),
                    'description'      => _('Engage your readers with your own personalized newsletter.'),
                    'long_description' => _(
                        '<p>It is more and more frequent that newspapers send bulletins with a selection
                        of the most interesting news of the day/week/month.</p>
                        <p>This module allows administrators to create personal layouts of the
                        bulletin and to edit the style before sending it.</p>
                        <p>This way you will be able to create the newsletter your style.</p>'
                    ),
                    'price'            => [
                        'month' => 30,
                        'usage' => [
                            'price' => 0.3,
                            'items' => 1000,
                            'type' => _('emails')
                        ]
                    ]
                ],
                [
                    'id'               => 'OPINION_MANAGER',
                    'plan'             => 'Base',
                    'type'             => 'module',
                    'name'             => _('Opinion'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Create and manage your polls, engage your audience and collect useful information.'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'POLL_MANAGER',
                    'plan'             => 'Profesional',
                    'type'             => 'module',
                    'name'             => _('Polls'),
                    'description'      => _(
                        'Create and manage your polls, <strong>engage</strong> '
                        .'your audience and <strong>collect</strong> useful information.'
                    ),
                    'long_description' => _(
                        '<p>Pools can be bars, pie charts, multiple response, etc</p>
                        <p>And the most beautiful aspect is that they are compatible and completely
                        available on any browsers in the world.</p>'
                    ),
                    'price'            => [
                        'month' => 15
                    ]
                ],
                [
                    'id'               => 'PROMOTIONAL_BAR',
                    'plan'             => 'Other',
                    'type'             => 'internal',
                    'name'             => _('Promotional bar'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'SCHEDULE_MANAGER',
                    'plan'             => 'Other',
                    'type'             => 'internal',
                    'name'             => _('Schedules'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'SETTINGS_MANAGER',
                    'plan'             => 'Base',
                    'type'             => 'internal',
                    'name'             => _('System wide settings'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'SPECIAL_MANAGER',
                    'plan'             => 'Other',
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
                    'plan'             => 'Other',
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
                    'plan'             => 'Base',
                    'type'             => 'module',
                    'name'             => _('Static pages'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'SYNC_MANAGER',
                    'plan'             => 'Silver',
                    'type'             => 'module',
                    'name'             => _('Frontpage synchronization'),
                    'description'      => _('Update your local frontpages by updating 1 frontpage'),
                    'long_description' => _(
                        '<p>Update your local frontpages by updating 1 frontpage!</p>
                        <p>Do you have more than 1 newspaper and you would like for the "home" pages to be synchronised?</p><p>No problem. By activating this module you will have all your news synchronised, if you have many local newspapers for instance and one main one, you can update the frontpage of all locals with news of the general newspaper.</p><p>If you modify a frontpage in the main newspaper the frontpage of local newspapers will update automatically too.</p><p>The only requirement is that all newspapers need to belong to the same group, so that the frontpage is stored in the one place.</p>'),
                    'price'            => [
                        'month' => 65
                    ]
                ],
                [
                    'id'               => 'TRASH_MANAGER',
                    'plan'             => 'Base',
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
                    'plan'             => 'Silver',
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
                    'plan'             => 'Silver',
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
                    'plan'             => 'Base',
                    'type'             => 'internal',
                    'name'             => _('UserVoice integration'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'VIDEO_MANAGER',
                    'plan'             => 'Profesional',
                    'type'             => 'module',
                    'name'             => _('Videos'),
                    'description'      => _('Add description...'),
                    'long_description' => _('Missed long description...'),
                ],
                [
                    'id'               => 'WIDGET_MANAGER',
                    'plan'             => 'Base',
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
                    'plan'             => 'Other',
                    'type'             => 'module',
                    'name'             => _('Paywall'),
                    'description'      => _('Make money while doing what you love.'),
                    'long_description' => _(
                        '<p>PayWall is a way to make money on your website\'s content by user subscriptions.</p>
                        <p>The News business is a very challenging business and advertising
                        alone often does not allow to newspapers to keep going. Add paywall to your
                        newspaper and you will be able to select articles that you want to sell. This way in
                        order to access this news users will need to register. </p>
                        <p>You will be able to set the payment/subscription the way you want
                        (weekly, monthly, annual, etc) and also add currency and % of taxes that
                        the item is subject to.</p>'
                    ),
                    'price'            => [
                        'month' => 95
                    ]
                ],
                [
                    'id'               => 'SUPPORT_NONE',
                    'plan'             => 'Support',
                    'type'             => 'internal',
                    'name'             => _('No support'),
                    'description'      => _(''),
                    'long_description' => _('Missed long description...'),
                    'price'            => [
                        'month' => 0
                    ]
                ],
                [
                    'id'               => 'SUPPORT_PRO',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'name'             => _('Profesional Support'),
                    'description'      => _('Get 10 hours of development per month to tune your site.'),
                    'long_description' => _('<ul>
                            <li>Modification and creation of widgets.</li>
                            <li>Ticket based support (SLA 24 hours max)</li>
                        </ul>'),
                    'price'            => [
                        'month' => 100
                    ]
                ],
                [
                    'id'               => 'SUPPORT_2',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'name'             => _('Support 2'),
                    'description'      => _('Get 40 hours of development per month to tune your site.'),
                    'long_description' => _(
                        '<ul>
                            <li>Cambios básicos en maquetas HTML/CSS</li>
                            <li>Soporte via tickets/e-mail  y hangout/skype</li>
                        </ul>'
                    ),
                    'price'            => [
                        'month' => 300
                    ]
                ],
                [
                    'id'               => 'SUPPORT_4',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'name'             => _('Support 4'),
                    'description'      => _('Get 80 hours of development per month to tune your site.'),
                    'long_description' => _('<ul><li>Modificación y Creación de widgets, cambios básicos en maquetas HTML/CSS, Cambio en disposición de plantillas de home, Portadillas de categorías, Nuevas distribuciones en noticia interior</li><li>Soporte via tickets/e-mail y hangout/skype</li></ul>'),
                    'price'            => [
                        'month' => 600
                    ]
                ],
                [
                    'id'               => 'SUPPORT_8',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'name'             => _('Support 8'),
                    'description'      => _('Get 160 hours of development per month to tune your site.'),
                    'long_description' => _('<ul><li>Modificación y Creación de widgets, cambios básicos en maquetas HTML/CSS, Creación de nuevas plantillas, estilos, maqueteros, Creación/modificación de plantillas para eventos, ocasiones, especiales, otras ediciones, etc.</li><li>Soporte via tickets/email, hangout/skype y Teléfono agente</li></ul>'),
                    'price'            => [
                        'month' => 1200
                    ]
                ],
                [
                    'id'               => 'SUPPORT_8_PLUS',
                    'plan'             => 'Support',
                    'type'             => 'service',
                    'name'             => _('Support 8+'),
                    'description'      => _('Get 240 hours of development per month to tune your site.'),
                    'long_description' => _('<ul><li>This Support is designed for all newspapers that need may need help including during the weekend.</li><li>Modificación y Creación de widgets, cambios básicos en maquetas HTML/CSS, Creación de nuevas plantillas, estilos, maqueteros, Creación/modificación de plantillas para eventos, ocasiones, especiales, otras ediciones, etc.</li></ul>'),
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
