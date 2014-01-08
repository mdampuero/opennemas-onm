<?php
/**
 * Defines the Instance class
 *
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  Onm
 **/
namespace Onm\Instance;

/**
 * Handles the instance operations
 *
 * @package Onm
 **/
class Instance
{
    /**
     * Initializes all the application values for the instance
     *
     * @return void
     **/
    public function boot()
    {
        // Transform all the intance settings into application constants.
        if (!is_array($this->settings)) {
            $this->settings = unserialize($this->settings);
        }

        if (empty($this->settings['MEDIA_URL'])) {
            $this->settings['MEDIA_URL'] = '/media/';
        }

        foreach ($this->settings as $key => $value) {
            define($key, $value);
        }

        $this->initInternalConstants();

        if ($this->internal_name !== 'onm_manager') {
            $this->initTheme();
        }
    }

    /**
     * Loads the theme configuration
     *
     * @return void
     **/
    public function initTheme()
    {
        $theme = include_once TEMPLATE_USER_PATH.'/init.php';

        $this->theme = $theme;
    }

    /**
     * Initializes all the internal application constants
     *
     * @return void
     */
    public function initInternalConstants()
    {
        define('CACHE_PREFIX', INSTANCE_UNIQUE_NAME);

        define('SITE_ADMIN_DIR', "admin");
        define('SITE_ADMIN_TMP_DIR', "tmp");
        define('SITE_ADMIN_PATH', SITE_PATH.'/'.SITE_ADMIN_DIR.'/');
        define('SITE_ADMIN_TMP_PATH', SITE_ADMIN_PATH.SITE_ADMIN_TMP_DIR.'/');
        $cachepath = APPLICATION_PATH.DS.'tmp'
            .DS.'instances'.DS.INSTANCE_UNIQUE_NAME;
        if (!file_exists($cachepath)) {
            mkdir($cachepath, 0755, true);
        }
        define('CACHE_PATH', realpath($cachepath));

        /**
         * Logging settings
         **/
        define('SYS_LOG_FILENAME', SYS_LOG_PATH.DS.INSTANCE_UNIQUE_NAME.'-application.log');

        /**
         * Media paths and urls configurations
         **/
        //TODO: All the MEDIA_* should be ported to use this constant
        define('INSTANCE_MEDIA', MEDIA_URL.INSTANCE_UNIQUE_NAME.DS);
        define('INSTANCE_MEDIA_PATH', SITE_PATH.DS."media".DS.INSTANCE_UNIQUE_NAME.DS);

        // External server or a local dir
        define('MEDIA_DIR', INSTANCE_UNIQUE_NAME);
        // Full path to the instance media files
        define('MEDIA_DIR_URL', MEDIA_URL.MEDIA_DIR.'/');

        // local path to write media (/path/to/media)
        define('MEDIA_PATH', SITE_PATH."media".DS.INSTANCE_UNIQUE_NAME);

        define('MEDIA_IMG_PATH_URL', MEDIA_URL.MEDIA_DIR.'/'.IMG_DIR);
        define('MEDIA_IMG_ABSOLUTE_URL', SITE_URL."media".'/'.MEDIA_DIR.'/'.IMG_DIR);
        // TODO: A Eliminar
        // TODO: delete from application
        define('MEDIA_IMG_PATH', MEDIA_PATH.DS.IMG_DIR);
        // TODO: delete from application
        define('MEDIA_IMG_PATH_WEB', MEDIA_URL.MEDIA_DIR.'/'.IMG_DIR);

        /**
        * Template settings
        **/
        define('TEMPLATE_USER_PATH', SITE_PATH.DS."themes".DS.TEMPLATE_USER.DS);
        define('TEMPLATE_USER_URL', "/themes".'/'.TEMPLATE_USER.'/');
    }

    /**
     * Returns the database name for the instance
     *
     * @return string the database name
     **/
    public function getDatabaseName()
    {
        return $this->settings['BD_DATABASE'];
    }
}
