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
                    'name'             => _('Advertisement'),
                    'description'      => _('You can add images or script tags to publish advertising banners'),
                    'long_description' => _('<p>Gain money with your Opennemas newspaper.</p><p>Thanks to this module all opennemas journals will be able to create, add and manage ads on any pages: Frontpage Home/Sections, Inner Articles, Opinions, Gallery, Media.</p><p>There are more than 15 types of ads.</p><p>Once this module is activated, the administrator of the newspaper will be able to upload all ads he/she wants and start to make money with the newspaper</p><p>n all Opennemas newspapers there are 2 kind of advertisements </p><ul><li>Static: images jpg, gif, flash (swf)<ul><li>This kind of ads needs to be uploaded to the manager and then select it into the campaigns</li><li>A date range can be selected to schedule advertising that we want to display only in certain days</li><li>All static ads need to be assigned to a position. At the bottom of the ad/campaign creation form a list of possible spots is displayed.</li><li>The adverts manager only shows total ads publications information. There are no reports per day, week, month and year.</li><li>Another way to manage static advertisements is through external adservers: <a href="http://help.opennemas.com/knowledgebase/articles/220705-como-gestionar-publicidad-desde-openx-opennemas/" target="_blank">OpenX</a> y <a href="http://help.opennemas.com/knowledgebase/articles/220701-como-gestionar-publicidad-google-doubleclick/" target="_blank">Google DFP</a>.</li></ul></li><li>Dynamic: javascript<ul><li>These banners come from agencies/advertisement pr/li><li>The agencies own ads servers that will send banners to Opennemas newspape/li><li>The agencies usually provide users with daily, weekly or monthly /li><li>Opennemas newspapers would be publishers of ad spaces for agencies.</li><li>This means that the newspapers has to be considered as a reseller of banners and should receive a benefit for it.</li><li>This benefit has to be agreed with the agencies and has nothing to do with Opennemas and/or Openhost</li></ul></li></ul>'),
                    'type'             => 'module',
                    'price'            => [
                        'month' => 35
                    ]
                ],
                [
                    'id'          => 'ADVANCED_SEARCH',
                    'plan'        => 'Base',
                    'name'        => _('Advanced search'),
                    'description' => _('It allows you to search for contents directly inside the manager'),
                    'type'        => 'module'
                ],
                [
                    'id'               => 'ALBUM_MANAGER',
                    'plan'             => 'Profesional',
                    'name'             => _('Albums'),
                    'description'      => _('Module to manage albums and galleries'),
                    'long_description' => _('<p>Add Video and Image Galleries to your content.</p><p>This module will allow you to create Photo Galleries, add video from YouTube, Vimeo, Dailymotion, MarcaTV, etc</p><p>And the most interesting fact is that the video manager is the same as youtube one, perfect consistency and performance.</p>'),
                    'type'             => 'module'
                ],
                [
                    'id'          => 'ARTICLE_MANAGER',
                    'plan'        => 'Base',
                    'name'        => _('Articles'),
                    'description' => _('Module for managing articles'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'AVANCED_ARTICLE_MANAGER',
                    'plan'        => 'Other',
                    'name'        => _('Advanced article options'),
                    'description' => _('Module to allow the second article signature'),
                    'type'        => 'module'
                ],
                [
                    'id'               => 'AVANCED_FRONTPAGE_MANAGER',
                    'plan'             => 'Other',
                    'name'             => _('Advanced frontpage managers'),
                    'description'      => _('Module for content personalization on frontpages'),
                    'long_description' => _('<p>Change your Frontpage every time you want.</p><p>Changing frontpage is more and more frequent in order to disrupt with daily monotony.</p><p>By activating this module you will be allowed to:<ul><li>Change background colour of the news, so that a column can gain a different style from others.</li><li>Change font size, so that you can give more weight to a title in the page.</li><li>Change colour of titles fonts, so that you can combine it with the different background.</li><li>Change of style: font, bold, italic, etc</li><li>Change the disposition of the image with respect to the text: right, left, above/below of the title, etc</li></ul></p>'),
                    'type'             => 'module',
                    'price'            => [
                        'month' => 30
                    ]
                ],
                [
                    'id'          => 'BLOG_MANAGER',
                    'plan'        => 'Other',
                    'name'        => _('Blog'),
                    'description' => _('Module to manage reviews with blog format'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'BOOK_MANAGER',
                    'plan'        => 'Other',
                    'name'        => _('Books'),
                    'description' => _('Module for managing book pages'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'CACHE_MANAGER',
                    'plan'        => 'Other',
                    'name'        => _('Cache manager'),
                    'description' => _('Module for managing the cache of pages'),
                    'type'        => 'internal'
                ],
                [
                    'id'          => 'CATEGORY_MANAGER',
                    'plan'        => 'Base',
                    'name'        => _('Category'),
                    'description' => _('Module for managing categories'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'COMMENT_MANAGER',
                    'plan'        => 'Base',
                    'name'        => _('Comments'),
                    'description' => _('Module for managing comments'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'CRONICAS_MODULES',
                    'plan'        => 'Other',
                    'name'        => _('Cronicas customizations'),
                    'description' => _('Module for managing Cronicas customizations'),
                    'type'        => 'internal'
                ],
                [
                    'id'          => 'FILE_MANAGER',
                    'plan'        => 'Base',
                    'name'        => _('Files'),
                    'description' => _('Allows the user to upload files'),
                    'type'        => 'module'
                ],
                [
                    'id'               => 'FORM_MANAGER',
                    'plan'             => 'Other',
                    'name'             => _('Forms'),
                    'description'      => _('Allows to create new custom forms'),
                    'long_description' => _('<p>Let your readers be contributors.</p><p>Our feature Opennemas Connect will allow readers to submit their news, so that the newspaper can become the "voice of people/Internet".</p><p>You will be able to create custom submission forms for your contributors so that they can share daily in the easiest way.</p><p>All contributions can be moderated.</p>'),
                    'type'             => 'internal',
                    'price'            => [
                        'month' => 15
                    ]
                ],
                [
                    'id'          => 'FRONTPAGE_MANAGER',
                    'plan'        => 'Base',
                    'name'        => _('Frontpages'),
                    'description' => _('Module for managing elements in frontpages'),
                    'type'        => 'module'
                ],
                [
                    'id'               => 'FRONTPAGES_LAYOUT',
                    'plan'             => 'Silver',
                    'name'             => _('Frontpages layout'),
                    'description'      => _('Allows to select different models for the frontpages'),
                    'long_description' => _('<p>Create and ManageFrontpageLayouts made by you, every time you want! Price 45EUR/month</p><p>This module has two distinct parts: Manual or Automatic Personal Frontpages Management.</p><h3>MANUAL</h3><p>Management Personal Frontpages/Frontpages Organization</p><p>You can change the appearance of Opennemas newspaper is a matter of seconds with this module. In this way you can select a different frontpage model on each of the sections frontpages.</p><p>The features of this manager are:</p><ul><li>Select different models in each of the frontpages.</li><li>Choice between 2 preset layouts: format frontpage and blog format.</li><li>Ability to create up to 5 different layout models (layout model price 50EUR)</li></ul><p>NOTE: The composition of these layouts will be managed/created by a user. The composition will not be automatic</p><h3>AUTOMATIC</h3><p>If you do not want to have to create layouts, the system can do it for you.</p><p>The advantages of this management are:<ul><li>Blog Format: automation of frontpages in blog style. Last 10 articles will be displayed.</li><li>Tagging: frontpages will be created by searching for news according to a tag/metakeyword/KeywordName</li><li>Topics/Themes/Special: tagging-like functionality with ability to customise the frontpage with a different logo/styles  (i.e. Special Olympic Games)</li></ul></p>'),
                    'type'             => 'module',
                    'price'            => [
                        'month' => 45
                    ]
                ],
                [
                    'id'          => 'IMAGE_MANAGER',
                    'plan'        => 'Base',
                    'name'        => _('Images'),
                    'description' => _('Allows user to upload images'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'KEYWORD_MANAGER',
                    'plan'        => 'Base',
                    'name'        => _('Keywords'),
                    'description' => _('Allows user to define keywords associated with url, mails and internal searches'),
                    'type'        => 'module'
                ],
                [
                    'id'               => 'KIOSKO_MANAGER',
                    'plan'             => 'Gold',
                    'name'             => _('Kiosko'),
                    'description'      => _('Create your own newsstand for publishing e-papers, magazines and others'),
                    'long_description' => _('<p>Let your readers download the pdf copy of your print newspaper.</p><p>If you would like to keep the print version of your newspaper in a newsstand like <a href="http://kiosko.net/" target="_blank">kiosko.net</a>, you just need to upload the full version or the frontpage and your users will be able to download it whenever they want.</p>'),
                    'type'             => 'module',
                    'price'            => [
                        'month' => 40
                    ]
                ],
                [
                    'id'          => 'LETTER_MANAGER',
                    'plan'        => 'Other',
                    'name'        => _('Letters'),
                    'description' => _('Allows user to publish letters sent to the director'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'LIBRARY_MANAGER',
                    'plan'        => 'Other',
                    'name'        => _('Library'),
                    'description' => _('With this module users can access all contents by date'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'MENU_MANAGER',
                    'plan'        => 'Base',
                    'name'        => _('Menus'),
                    'description' => _('Allows user to manage the menús'),
                    'type'        => 'module'
                ],
                [
                    'id'               => 'NEWS_AGENCY_IMPORTER',
                    'plan'             => 'Gold',
                    'name'             => _('News Agency importer'),
                    'description'      => _('Keeping your digital news up to date with agencies is already a reality!'),
                    'long_description' => _('<p>Keeping your digital news up to date with agencies is already a reality!</p><p><a href="http://www.efe.com/" target="_blank">Agencia EFE</a>, <a href="http://www.europapress.es/" target="_blank">agencia Europa press</a>, <a href="http://www.reuters.com/" target="_blank">Reuters</a>, etc Every and each of this channels will be available for opennemas newspapers. With just few clicks the administrator will be able to add any news from agencies in the frontpage of the newspaper together with any image or media attached.</p>'),
                    'type'             => 'module',
                    'price'            => [
                        'month' => 40
                    ]
                ],
                [
                    'id'               => 'NEWSLETTER_MANAGER',
                    'plan'             => 'Silver',
                    'name'             => _('Newsletter'),
                    'description'      => _('Engage your readers with your own personalized newsletter.'),
                    'long_description' => _('<p>Engage your readers with your own personalized newsletter.</p><p>It is more and more frequent that newspapers send bulletins with a selection of the most interesting news of the day/week/month.</p><p>This module allows administrators to create personal layouts of the bulletin and to edit the style before sending it.</p><p>This way you will be able to create the newsletter your style.</p>'),
                    'type'             => 'module',
                    'price'            => [
                        'month' => 30,
                        'usage' => [
                            'price' => 0.3,
                            'items' => 1000,
                            'type' => 'emails'
                        ]
                    ]
                ],
                [
                    'id'   => 'OPINION_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Opinion'),
                    'type' => 'module'
                ],
                [
                    'id'               => 'POLL_MANAGER',
                    'plan'             => 'Profesional',
                    'name'             => _('Polls'),
                    'description'      => _('Create and manage your polls, engage your audience and collect useful information.'),
                    'long_description' => _('<p>Create and manage your polls, engage your audience and collect useful information.</p><p>Pools can be bars, pie charts, multiple response, etc</p><p>And the most beautiful aspect is that they are compatible and completely available on any browsers in the world.</p>'),
                    'type'             => 'module',
                    'price'            => [
                        'month' => 15
                    ]
                ],
                [
                    'id'          => 'PROMOTIONAL_BAR',
                    'plan'        => 'Other',
                    'name'        => _('Promotional bar'),
                    'description' => _('Add description...'),
                    'type'        => 'internal'
                ],
                [
                    'id'          => 'SCHEDULE_MANAGER',
                    'plan'        => 'Other',
                    'name'        => _('Schedules'),
                    'description' => _('Add description...'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'SETTINGS_MANAGER',
                    'plan'        => 'Base',
                    'name'        => _('System wide settings'),
                    'description' => _('Add description...'),
                    'type'        => 'internal'
                ],
                [
                    'id'          => 'SPECIAL_MANAGER',
                    'plan'        => 'Other',
                    'name'        => _('Specials'),
                    'description' => _('Add description...'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'STATIC_LIBRARY',
                    'plan'        => 'Other',
                    'name'        => _('Static library'),
                    'description' => _('Add description...'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'STATIC_PAGES_MANAGER',
                    'plan'        => 'Base',
                    'name'        => _('Static pages'),
                    'description' => _('Add description...'),
                    'type'        => 'module'
                ],
                [
                    'id'               => 'SYNC_MANAGER',
                    'plan'             => 'Silver',
                    'name'             => _('Instance synchronization'),
                    'description'      => _('Update your local frontpages by updating 1 frontpage'),
                    'long_description' => _('<p>Update your local frontpages by updating 1 frontpage!</p><p>Do you have more than 1 newspaper and you would like for the "home" pages to be synchronised?</p><p>No problem. By activating this module you will have all your news synchronised, if you have many local newspapers for instance and one main one, you can update the frontpage of all locals with news of the general newspaper.</p><p>If you modify a frontpage in the main newspaper the frontpage of local newspapers will update automatically too.</p><p>The only requirement is that all newspapers need to belong to the same group, so that the frontpage is stored in the one place.</p>'),
                    'type'             => 'module',
                    'price'            => [
                        'month' => 65
                    ]
                ],
                [
                    'id'          => 'TRASH_MANAGER',
                    'plan'        => 'Base',
                    'name'        => _('Trash'),
                    'description' => _('Add description...'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'USER_GROUP_MANAGER',
                    'plan'        => 'Silver',
                    'name'        => _('User groups'),
                    'description' => _('Add description...'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'USER_MANAGER',
                    'plan'        => 'Silver',
                    'name'        => _('Users'),
                    'description' => _('Add description...'),
                    'type'        => 'module'
                ],
                [
                    'id'          => 'USERVOICE_SUPPORT',
                    'plan'        => 'Base',
                    'name'        => _('UserVoice integration'),
                    'description' => _('Add description...'),
                    'type'        => 'internal'
                ],
                [
                    'id'   => 'VIDEO_MANAGER',
                    'plan' => 'Profesional',
                    'name' => _('Videos'),
                    'type' => 'module'
                ],
                [
                    'id'          => 'WIDGET_MANAGER',
                    'plan'        => 'Base',
                    'name'        => _('Widgets'),
                    'description' => _('Add description...'),
                    'type'        => 'module'
                ],
                [
                    'id'               => 'PAYWALL',
                    'plan'             => 'Other',
                    'name'             => _('Paywall'),
                    'description'      => _('Add description...'),
                    'long_description' => _('<p>Make money while doing what you love.</p><p>The News business is a very challenging business and advertising alone often does not allow to newspapers to keep going. Add paywall to your newspaper and you will be able to select articles that you want to sell. This way in order to access this news users will need to register. </p><p>You will be able to set the payment/subscription the way you want (weekly, monthly, annual, etc) and also add currency and % of taxes that the item is subject to.</p>'),
                    'type'             => 'module',
                    'price'            => [
                        'month' => 95
                    ]
                ],
                [
                    'id'          => 'SUPPORT_NONE',
                    'plan'        => 'Support',
                    'name'        => _('No support'),
                    'description' => '',
                    'type'        => 'internal'
                ],
                [
                    'id'          => 'SUPPORT_PRO',
                    'plan'        => 'Support',
                    'name'        => _('Profesional Support'),
                    'description' => _('10 hours/month'),
                    'type'        => 'service'
                ],
                [
                    'id'          => 'SUPPORT_2',
                    'plan'        => 'Support',
                    'name'        => _('Support 2'),
                    'description' => _('40 hours/month'),
                    'type'        => 'service'
                ],
                [
                    'id'          => 'SUPPORT_4',
                    'plan'        => 'Support',
                    'name'        => _('Support 4'),
                    'description' => _('80 hours/month'),
                    'type'        => 'service'
                ],
                [
                    'id'          => 'SUPPORT_8',
                    'plan'        => 'Support',
                    'name'        => _('Support 8'),
                    'description' => _('160 hours/month'),
                    'type'        => 'service'
                ],
                [
                    'id'          => 'SUPPORT_8_PLUS',
                    'plan'        => 'Support',
                    'name'        => _('Support 8+'),
                    'description' => _('240 hours/month'),
                    'type'        => 'service'
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
