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
                'LOG_SQL'                   => _('SQL Log'),
                'MENU_MANAGER'              => _('Menus'),
                'NEWS_AGENCY_IMPORTER'      => _('News Agency importer'),
                'NEWSLETTER_MANAGER'        => _('Newsletter'),
                'OPINION_MANAGER'           => _('Opinion'),
                'PAPER_IMPORT'              => _('Paper import'),
                'POLL_MANAGER'              => _('Polls'),
                'PROMOTIONAL_BAR'           => _('Promotional bar'),
                'SCHEDULE_MANAGER'          => _('Schedules'),
                'SETTINGS_MANAGER'          => _('System wide settings'),
                'SPECIAL_MANAGER'           => _('Specials'),
                'STATIC_LIBRARY'            => _('Static library'),
                'STATIC_PAGES_MANAGER'      => _('Static pages'),
                'SUPPORT_NONE'              => _('No Support'),
                'SUPPORT_PRO'               => _('Profesional Support'),
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
                'VIDEO_LOCAL_MANAGER'       => _('Videos (local)'),
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
                    'id'   => 'ADS_MANAGER',
                    'plan' => 'Profesional',
                    'name' => _('Advertisement'),
                    'description' => _('You can add images or script tags to publish advertising banners')
                ],
                [
                    'id'   => 'ADVANCED_SEARCH',
                    'plan' => 'Base',
                    'name' => _('Advanced search'),
                    'description' => _('It allows you to search for contents directly inside the manager')
                ],
                [
                    'id'   => 'ALBUM_MANAGER',
                    'plan' => 'Profesional',
                    'name' => _('Albums'),
                    'description' => _('Module to manage albums and galleries')
                ],
                [
                    'id'   => 'ARTICLE_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Articles'),
                    'description' => _('Module for managing articles')
                ],
                [
                    'id'   => 'AVANCED_ARTICLE_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Advanced article options'),
                    'description' => _('Module to allow the second article signature')
                ],
                [
                    'id'   => 'AVANCED_FRONTPAGE_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Advanced frontpage managers'),
                    'description' => _('Module for content personalization on frontpages')
                ],
                [
                    'id'   => 'BLOG_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Blog'),
                    'description' => _('Module to manage reviews with blog format')
                ],
                [
                    'id'   => 'BOOK_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Books'),
                    'description' => _('Module for managing book pages')
                ],
                [
                    'id'   => 'CACHE_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Cache manager'),
                    'description' => _('Module for managing the cache of pages')
                ],
                [
                    'id'   => 'CATEGORY_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Category'),
                    'description' => _('Module for managing categories')
                ],
                [
                    'id'   => 'COMMENT_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Comments'),
                    'description' => _('Module for managing comments')
                ],
                [
                    'id'   => 'CRONICAS_MODULES',
                    'plan' => 'Other',
                    'name' => _('Cronicas customizations'),
                    'description' => _('Module for managing Cronicas customizations')
                ],
                [
                    'id'   => 'FILE_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Files'),
                    'description' => _('Allows the user to upload files')
                ],
                [
                    'id'   => 'FORM_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Forms'),
                    'description' => _('Allows to create new custom forms')
                ],
                [
                    'id'   => 'FRONTPAGE_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Frontpages'),
                    'description' => _('Module for managing elements in frontpages')
                ],
                [
                    'id'   => 'FRONTPAGES_LAYOUT',
                    'plan' => 'Silver',
                    'name' => _('Frontpages layout'),
                    'description' => _('Allows to select different models for the frontpages')
                ],
                [
                    'id'   => 'IMAGE_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Images'),
                    'description' => _('Allows user to upload images')
                ],
                [
                    'id'   => 'KEYWORD_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Keywords'),
                    'description' => _('Allows user to define keywords associated with url, mails and internal searches')
                ],
                [
                    'id'   => 'KIOSKO_MANAGER',
                    'plan' => 'Gold',
                    'name' => _('Kiosko'),
                    'description' => _('Create your own newsstand for publishing e-papers, magazines and others')
                ],
                [
                    'id'   => 'LETTER_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Letters'),
                    'description' => _('Allows user to publish letters sent to the director')
                ],
                [
                    'id'   => 'LIBRARY_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Library'),
                    'description' => _('With this module users can access all contents by date')
                ],
                [
                    'id'   => 'LOG_SQL',
                    'plan' => 'Other',
                    'name' => _('SQL Log'),
                    'description' => _('Internal module to check sql errors')
                ],
                [
                    'id'   => 'MENU_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Menus'),
                    'description' => _('Allows user to manage the menús')
                ],
                [
                    'id'   => 'NEWS_AGENCY_IMPORTER',
                    'plan' => 'Gold',
                    'name' => _('News Agency importer'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'NEWSLETTER_MANAGER',
                    'plan' => 'Silver',
                    'name' => _('Newsletter'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'OPINION_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Opinion'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'PAPER_IMPORT',
                    'plan' => 'Other',
                    'name' => _('Paper import'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'POLL_MANAGER',
                    'plan' => 'Profesional',
                    'name' => _('Polls'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'PROMOTIONAL_BAR',
                    'plan' => 'Other',
                    'name' => _('Promotional bar'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'SCHEDULE_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Schedules'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'SETTINGS_MANAGER',
                    'plan' => 'Base',
                    'name' => _('System wide settings'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'SPECIAL_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Specials'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'STATIC_LIBRARY',
                    'plan' => 'Other',
                    'name' => _('Static library'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'STATIC_PAGES_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Static pages'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'SYNC_MANAGER',
                    'plan' => 'Silver',
                    'name' => _('Instance synchronization'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'TRASH_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Trash'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'USER_GROUP_MANAGER',
                    'plan' => 'Silver',
                    'name' => _('User groups'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'USER_MANAGER',
                    'plan' => 'Silver',
                    'name' => _('Users'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'USERVOICE_SUPPORT',
                    'plan' => 'Base',
                    'name' => _('UserVoice integration'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'VIDEO_MANAGER',
                    'plan' => 'Profesional',
                    'name' => _('Videos'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'VIDEO_LOCAL_MANAGER',
                    'plan' => 'Profesional',
                    'name' => _('Videos (local)'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'WIDGET_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Widgets'),
                    'description' => _('Add description...')
                ],
                [
                    'id'   => 'PAYWALL',
                    'plan' => 'Other',
                    'name' => _('Paywall'),
                    'description' => _('Add description...')
                ],
                [
                    'id' => 'SUPPORT_NONE',
                    'plan' => 'Support',
                    'name' => _('No support'),
                    'description' => ''
                ],
                [
                    'id' => 'SUPPORT_PRO',
                    'plan' => 'Support',
                    'name' => _('Profesional Support'),
                    'description' => _('10 hours/month')
                ],
                [
                    'id' => 'SUPPORT_2',
                    'plan' => 'Support',
                    'name' => _('Support 2'),
                    'description' => _('40 hours/month')
                ],
                [
                    'id' => 'SUPPORT_4',
                    'plan' => 'Support',
                    'name' => _('Support 4'),
                    'description' => _('80 hours/month')
                ],
                [
                    'id' => 'SUPPORT_8',
                    'plan' => 'Support',
                    'name' => _('Support 8'),
                    'description' => _('160 hours/month')
                ],
                [
                    'id' => 'SUPPORT_8_PLUS',
                    'plan' => 'Support',
                    'name' => _('Support 8+'),
                    'description' => _('240 hours/month')
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
