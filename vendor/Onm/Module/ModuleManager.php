<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Module;

/**
 * Class for handling activated and available modules.
 *
 * @package    Onm
 * @subpackage Module
 * @author     Fran Dieguez <fran@openhost.es>
 * @version    SVN: $Id: Module.php 28842 Xov Xuñ 23 12:24:17 2011 frandieguez $
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
     * Initilizes the object.
     *
     * @param array $params parameters for initilizing the module manager.
     */
    public function __construct($params = array())
    {
        self::getActivatedModules();
    }

    /**
     * Returns the activated modules.
     *
     *
     * @return array array of activated modules
     *
     * @throws <b>Exception</b> if something went wrong
     */
    public static function getActivatedModules()
    {
        //global $activatedModules;
        $activatedModules = \Onm\Settings::get('activated_modules');

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
        if ( !isset(self::$availableModules) ) {
            self::$availableModules = array(
                'ADS_MANAGER',
                'ADVANCED_SEARCH',
                'ALBUM_MANAGER',
                'ARTICLE_MANAGER',
                'CACHE_MANAGER',
                'CATEGORY_MANAGER',
                'COMMENT_MANAGER',
                'COMMENT_DISQUS_MANAGER',
                'EFE_FILE_IMPORTER',
                'EFE_IMPORTER',
                'EUROPAPRESS_IMPORTER',
                'FILE_MANAGER',
                'FRONTPAGE_MANAGER',
                'IMAGE_MANAGER',
                'KEYWORD_MANAGER',
                'KIOSKO_MANAGER',
                'LINK_CONTROL_MANAGER',
                'MENU_MANAGER',
                'MYSQL_MANAGER',
                'NEWSLETTER_MANAGER',
                'ONM_STATISTICS',
                'OPINION_MANAGER',
                'PAPER_IMPORT',
                'PHP_CACHE_MANAGER',
                'POLL_MANAGER',
                'PRIVILEGE_MANAGER',
                'SETTINGS_MANAGER',
                'STATIC_PAGES_MANAGER',
                'SYSTEM_UPDATE_MANAGER',
                'TRASH_MANAGER',
                'USER_GROUP_MANAGER',
                'USER_MANAGER',
                'USERVOICE_SUPPORT',
                'VIDEO_MANAGER',
                'WIDGET_MANAGER',
                'LOG_SQL',
                'BOOK_MANAGER',
                'SPECIAL_MANAGER',
                'SCHEDULE_MANAGER',
                'AVANCED_ARTICLE_MANAGER',
                'LIBRARY_MANAGER',
                'LETTER_MANAGER',
                'SYNC_MANAGER',
                'FRONTPAGES_LIBRARY',
                'STATIC_LIBRARY',
                'CRONICAS_MODULES',
                'AVANCED_FRONTPAGE_MANAGER',
            );
        }

        return self::$availableModules;
    }

    /**
     * Returns if all modules are activated.
     *
     * @return boolean true if all modules are activated
     */
    public static function checkAllModulesActivated()
    {
        $activatedModules = \Onm\Settings::get('activated_modules');
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
        if (!isset($module) || empty($module)) {
            // Check if module name is properly setted

            return true;
        } elseif (self::checkAllModulesActivated()) {
            // Check if all modules are activated

            return true;
        } elseif (!self::moduleExists($module)) {
            // Check if module exists

            throw new ModuleException("Module '{$module}'not available");
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
             m::add(_("Sorry, you don't have enought privileges"));
             Application::forward('/admin/');
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
        return in_array($moduleName, self::getAvailableModules());
    }
}
