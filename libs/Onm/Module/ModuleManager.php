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
                'POLL_MANAGER'              => _('Polls'),
                'PROMOTIONAL_BAR'           => _('Promotional bar'),
                'SCHEDULE_MANAGER'          => _('Schedules'),
                'SETTINGS_MANAGER'          => _('System wide settings'),
                'SPECIAL_MANAGER'           => _('Specials'),
                'STATIC_LIBRARY'            => _('Static library'),
                'STATIC_PAGES_MANAGER'      => _('Static pages'),
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
                    'name' => _('Advertisement')
                ],
                [
                    'id'   => 'ADVANCED_SEARCH',
                    'plan' => 'Base',
                    'name' => _('Advanced search'),
                ],
                [
                    'id'   => 'ALBUM_MANAGER',
                    'plan' => 'Profesional',
                    'name' => _('Albums'),
                ],
                [
                    'id'   => 'ARTICLE_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Articles'),
                ],
                [
                    'id'   => 'AVANCED_ARTICLE_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Advanced article options'),
                ],
                [
                    'id'   => 'AVANCED_FRONTPAGE_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Advanced frontpage managers'),
                ],
                [
                    'id'   => 'BLOG_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Blog'),
                ],
                [
                    'id'   => 'BOOK_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Books'),
                ],
                [
                    'id'   => 'CACHE_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Cache manager'),
                ],
                [
                    'id'   => 'CATEGORY_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Category'),
                ],
                [
                    'id'   => 'COMMENT_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Comments'),
                ],
                [
                    'id'   => 'CRONICAS_MODULES',
                    'plan' => 'Other',
                    'name' => _('Cronicas customizations'),
                ],
                [
                    'id'   => 'FILE_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Files'),
                ],
                [
                    'id'   => 'FORM_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Forms'),
                ],
                [
                    'id'   => 'FRONTPAGE_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Frontpages'),
                ],
                [
                    'id'   => 'FRONTPAGES_LAYOUT',
                    'plan' => 'Silver',
                    'name' => _('Frontpages layout'),
                ],
                [
                    'id'   => 'IMAGE_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Images'),
                ],
                [
                    'id'   => 'KEYWORD_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Keywords'),
                ],
                [
                    'id'   => 'KIOSKO_MANAGER',
                    'plan' => 'Gold',
                    'name' => _('Kiosko'),
                ],
                [
                    'id'   => 'LETTER_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Letters'),
                ],
                [
                    'id'   => 'LIBRARY_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Library'),
                ],
                [
                    'id'   => 'LOG_SQL',
                    'plan' => 'Other',
                    'name' => _('SQL Log'),
                ],
                [
                    'id'   => 'MENU_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Menus'),
                ],
                [
                    'id'   => 'NEWS_AGENCY_IMPORTER',
                    'plan' => 'Gold',
                    'name' => _('News Agency importer'),
                ],
                [
                    'id'   => 'NEWSLETTER_MANAGER',
                    'plan' => 'Silver',
                    'name' => _('Newsletter'),
                ],
                [
                    'id'   => 'OPINION_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Opinion'),
                ],
                [
                    'id'   => 'POLL_MANAGER',
                    'plan' => 'Profesional',
                    'name' => _('Polls'),
                ],
                [
                    'id'   => 'PROMOTIONAL_BAR',
                    'plan' => 'Other',
                    'name' => _('Promotional bar'),
                ],
                [
                    'id'   => 'SCHEDULE_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Schedules'),
                ],
                [
                    'id'   => 'SETTINGS_MANAGER',
                    'plan' => 'Base',
                    'name' => _('System wide settings'),
                ],
                [
                    'id'   => 'SPECIAL_MANAGER',
                    'plan' => 'Other',
                    'name' => _('Specials'),
                ],
                [
                    'id'   => 'STATIC_LIBRARY',
                    'plan' => 'Other',
                    'name' => _('Static library'),
                ],
                [
                    'id'   => 'STATIC_PAGES_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Static pages'),
                ],
                [
                    'id'   => 'SYNC_MANAGER',
                    'plan' => 'Silver',
                    'name' => _('Instance synchronization'),
                ],
                [
                    'id'   => 'TRASH_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Trash'),
                ],
                [
                    'id'   => 'USER_GROUP_MANAGER',
                    'plan' => 'Silver',
                    'name' => _('User groups'),
                ],
                [
                    'id'   => 'USER_MANAGER',
                    'plan' => 'Silver',
                    'name' => _('Users'),
                ],
                [
                    'id'   => 'USERVOICE_SUPPORT',
                    'plan' => 'Base',
                    'name' => _('UserVoice integration'),
                ],
                [
                    'id'   => 'VIDEO_MANAGER',
                    'plan' => 'Profesional',
                    'name' => _('Videos'),
                ],
                [
                    'id'   => 'VIDEO_LOCAL_MANAGER',
                    'plan' => 'Profesional',
                    'name' => _('Videos (local)'),
                ],
                [
                    'id'   => 'WIDGET_MANAGER',
                    'plan' => 'Base',
                    'name' => _('Widgets'),
                ],
                [
                    'id'   => 'PAYWALL',
                    'plan' => 'Others',
                    'name' => _('Paywall'),
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
