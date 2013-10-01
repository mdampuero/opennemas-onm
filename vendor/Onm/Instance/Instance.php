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
     * Initializes the instance object
     *
     * @return void
     **/
    public function __construct()
    {
    }

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
        if (!empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
            || $_SERVER['SERVER_PORT'] == 443
        ) {
            $protocol = "https://";
        } else {
            $protocol = "http://";
        }

        define('SS', "/");

        define('CACHE_PREFIX', INSTANCE_UNIQUE_NAME);

        define('SITE', $_SERVER['SERVER_NAME']);

        define('BASE_URL', '/');
        define('ADMIN_DIR', "admin");
        define('SITE_URL', $protocol.SITE.BASE_URL);
        define('SITE_URL_ADMIN', SITE_URL.ADMIN_DIR);

        define('SITE_ADMIN_DIR', "admin");
        define('SITE_ADMIN_TMP_DIR', "tmp");
        define('SITE_ADMIN_PATH', SITE_PATH.SS.SITE_ADMIN_DIR.SS);
        define('SITE_ADMIN_TMP_PATH', SITE_ADMIN_PATH.SITE_ADMIN_TMP_DIR.SS);
        $cachepath = APPLICATION_PATH.DS.'tmp'
            .DS.'instances'.DS.INSTANCE_UNIQUE_NAME;
        if (!file_exists($cachepath)) {
            mkdir($cachepath, 0755, true);
        }
        define('CACHE_PATH', realpath($cachepath));

        $commonCachepath = APPLICATION_PATH.DS.'tmp'.DS.'instances'.DS.'common';
        if (!file_exists($commonCachepath)) {
            mkdir($commonCachepath, 0755, true);
        }
        define('COMMON_CACHE_PATH', realpath($commonCachepath));

        // Backup paths
        define('BACKUP_PATH', SITE_PATH.DS.'..'.DS."tmp/backups");

        /**
         * Logging settings
         **/
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('SYS_LOG_FILENAME', SYS_LOG_PATH.DS.INSTANCE_UNIQUE_NAME.'-application.log');

        // TODO: delete from application
        define('SYS_NAME_GROUP_ADMIN', 'Administrador');

        /**
         * Media paths and urls configurations
         **/
        //TODO: All the MEDIA_* should be ported to use this constant
        define('INSTANCE_MEDIA', MEDIA_URL.INSTANCE_UNIQUE_NAME.DS);
        define('INSTANCE_MEDIA_PATH', SITE_PATH.DS."media".DS.INSTANCE_UNIQUE_NAME.DS);

        define('STATIC_PAGE_PATH', 'estaticas');

        // External server or a local dir
        define('MEDIA_DIR', INSTANCE_UNIQUE_NAME);
        // Full path to the instance media files
        define('MEDIA_DIR_URL', MEDIA_URL.MEDIA_DIR.SS);

        // local path to write media (/path/to/media)
        define('MEDIA_PATH', SITE_PATH."media".DS.INSTANCE_UNIQUE_NAME);
        define('IMG_DIR', "images");
        define('FILE_DIR', "files");
        define('ADS_DIR', "advertisements");
        define('OPINION_DIR', "opinions");

        define('MEDIA_IMG_PATH_URL', MEDIA_URL.MEDIA_DIR.SS.IMG_DIR);
        define('MEDIA_IMG_ABSOLUTE_URL', SITE_URL."media".SS.MEDIA_DIR.SS.IMG_DIR);
        // TODO: A Eliminar
        // TODO: delete from application
        define('MEDIA_IMG_PATH', MEDIA_PATH.DS.IMG_DIR);
        // TODO: delete from application
        define('MEDIA_IMG_PATH_WEB', MEDIA_URL.MEDIA_DIR.SS.IMG_DIR);

        /**
        * Template settings
        **/
        define('TEMPLATE_USER_PATH', SITE_PATH.DS."themes".DS.TEMPLATE_USER.DS);
        define('TEMPLATE_USER_URL', "/themes".SS.TEMPLATE_USER.SS);

        define('TEMPLATE_ADMIN', "admin");
        define('TEMPLATE_ADMIN_PATH', SITE_PATH.DS.DS."themes".DS.TEMPLATE_ADMIN.SS);
        define('TEMPLATE_ADMIN_PATH_WEB', SS."themes".SS.TEMPLATE_ADMIN.SS);
        define('TEMPLATE_ADMIN_URL', SS."themes".SS.TEMPLATE_ADMIN.SS);
        define('ADVERTISEMENT_ENABLE', true);

        define('TEMPLATE_MANAGER', "manager");

        /**
         * Mail settings
         **/
        define('MAIL_HOST', "localhost");
        // 217.76.146.62, ssl://smtp.gmail.com:465, ssl://smtp.gmail.com:587
        define('MAIL_USER', "");
        define('MAIL_PASS', "");
        define('MAIL_FROM', 'noreply@opennemas.com');

        /**
        * Session de usuario
        **/
        $GLOBALS['USER_ID'] = null;
        $GLOBALS['conn'] = null;

        define('ITEMS_PAGE', "20"); // TODO: delete from application
    }
}
