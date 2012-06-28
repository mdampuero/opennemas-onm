<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s;
/**
 * Main application class, handles all the initialization of the app
 *
 * @package    Onm
 * @subpackage Core
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class Application
{
    public $conn           = null;
    public $logger         = null;
    public $errors         = array();
    public $adodb          = null;
    public $smarty         = null;
    public $log            = null;
    public $template       = null;
    public $sesion         = null;
    public $cache          = null;
    public $events         = array();
    public static $language    = '';
    public static $request        = null;

    /**
    * Setup the Application instance and assigns it to a global variable
    *
    * If global variable application doesn't exists create an instance of it,
    * and setup up DB conection, Adodb logger instance, Workflow
    * logger instance,
    *
    * @return object $GLOBALS['application']
    */
    public static function load()
    {
        self::initEnvironment(ENVIRONMENT);

        if (!isset($GLOBALS['application']) || $GLOBALS['application']==null) {
            // Setting up static Constants
            self::initInternalConstants();

            $GLOBALS['application'] = new Application();

            if (INSTANCE_UNIQUE_NAME != 'onm_manager') {
                // Setting up DataBase connection
                self::initDatabase();

                // Setting up Logger
                self::initLogger();

                // Setting up Gettext
                self::initL10nSystem();

                self::initTimeZone();
            }
        }

        return $GLOBALS['application'];
    }

    public static function initDatabase()
    {
        // Database
        $GLOBALS['application']->conn = \ADONewConnection(BD_TYPE);
        $GLOBALS['application']->conn->Connect(BD_HOST,
            BD_USER, BD_PASS, BD_DATABASE);

        // Check if adodb is log enabled
        if (s::get('log_db_enabled') == 1) {
            $GLOBALS['application']->conn->LogSQL();
        }
    }

    public static function initLogger()
    {
        // init Logger
        $logLevel = (s::get('log_level'))?: 'normal';
        $logger = new \Onm\Log($logLevel);
        Zend_Registry::set('logger', $logger);

        // Composite Logger (file + mail)
        // http://www.indelible.org/php/Log/guide.html#composite-handlers
        if ( s::get('log_enabled') == 1) {
            $GLOBALS['application']->logger = \Log::singleton('composite');

            $conf = array('mode' => 0600,
                          'timeFormat' => '[%Y-%m-%d %H:%M:%S]',
                          'lineFormat' => '%1$s %2$s [%3$s] %4$s %5$s %6$s');
            $fileLogger = &Log::singleton('file',
                SYS_LOG_FILENAME, 'application', $conf);
            $GLOBALS['application']->logger->addChild($fileLogger);
        } else {
            $GLOBALS['application']->logger = \Log::singleton('null');
        }
    }

    /**
     * Set up gettext translations.
     */
    public static function initL10nSystem()
    {
        $timezone = s::get('time_zone');
        if (isset($timezone)) {
            $availableTimezones = \DateTimeZone::listIdentifiers();
            date_default_timezone_set($availableTimezones[$timezone]);
        }

        /* Set internal character encoding to UTF-8 */
        mb_internal_encoding("UTF-8");

        $availableLanguages = self::getAvailableLanguages();
        $forceLanguage = filter_input(INPUT_GET,
            'language', FILTER_SANITIZE_STRING);

        if ($forceLanguage !== null
            && in_array($forceLanguage, array_keys($availableLanguages))
        ) {
            self::$language = $forceLanguage;
        } else {
            self::$language = s::get('site_language');
        }

        $locale = self::$language.".UTF-8";
        $domain = 'messages';

        if (self::isBackend()) {
            $localeDir = SITE_ADMIN_PATH.DS.'locale'.DS;
        } else {
            $localeDir = SITE_PATH.DS.'locale'.DS;
        }

        if (isset($_GET["locale"])) {
            $locale = $_GET["locale"].'.UTF-8';
        }

        putenv("LC_MESSAGES=$locale");
        setlocale(LC_ALL, $locale);
        bindtextdomain($domain, $localeDir);
        textdomain($domain);

    }

    /**
     * Sets the timezone for this app from the instance settings
     *
     * @return void
     **/
    public static function initTimeZone()
    {
        if ($timezone = s::get('time_zone')) {
            $availableTimeZones = \DateTimeZone::listIdentifiers();
            date_default_timezone_set($availableTimeZones[(int) $timezone]);
        }
    }

    /**
    * Loads all the common libraries and the packages passed as argument
    *
    * @param array $packages list of packages to load
    */
    public static function initAutoloader()
    {

        // TODO: move to autoload.php
        // Load required libraries
        require_once SITE_LIBS_PATH.'/functions.php';
        require_once SITE_VENDOR_PATH.'/adodb5/adodb.inc.php';
        require_once SITE_VENDOR_PATH.'/Pager/Pager.php';
        require_once SITE_VENDOR_PATH.'/smarty/smarty-legacy/Smarty.class.php';
        require_once SITE_VENDOR_PATH.'/Log.php';
        require_once SITE_VENDOR_PATH.'/Template.php';
        require_once SITE_VENDOR_PATH.'/Restler/restler.php';
        require_once SITE_VENDOR_PATH.'/Restler/xmlformat.php';
    }

    /*
     * Initializes all the internal application constants
     *
     */
    public static function initInternalConstants()
    {
        /**
         * System setup
         **/
        define('STATUS', "1");
        define('CHARSET', "text/html; charset=UTF-8");

        $protocol = 'http://';
        if (preg_match('@^/admin/@', $_SERVER['REQUEST_URI'])) {
            $protocol = (!empty($_SERVER['HTTPS']))? 'https://': 'http://';
        }

        define('SS', "/");

        define('APC_PREFIX', INSTANCE_UNIQUE_NAME);

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

        /**
         * Logging settings
         **/
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('SYS_LOG_FILENAME', SYS_LOG_PATH.DS.'application.log');
        define('SYS_SESSION_PATH', $cachepath.DS."/sessions".DS);
        define('OPENNEMAS_BACKEND_SESSIONS', SYS_SESSION_PATH.'backend/');
        define('OPENNEMAS_FRONTEND_SESSIONS', SYS_SESSION_PATH.'frontend/');
        if (!file_exists(SYS_SESSION_PATH) ) {
            mkdir(SYS_SESSION_PATH);
        }
        if (!file_exists(OPENNEMAS_BACKEND_SESSIONS) ) {
            mkdir(OPENNEMAS_BACKEND_SESSIONS);
        }
        if (!file_exists(OPENNEMAS_FRONTEND_SESSIONS)) {
            mkdir(OPENNEMAS_FRONTEND_SESSIONS);
        }

        // TODO: delete from application
        define('SYS_NAME_GROUP_ADMIN', 'Administrador');

        /**
         * Media paths and urls configurations
         **/
        //TODO: All the MEDIA_* should be ported to use this constant
        define('INSTANCE_MEDIA', MEDIA_URL.INSTANCE_UNIQUE_NAME.DS);
        define('INSTANCE_MEDIA_PATH',
            SITE_PATH.DS."media".DS.INSTANCE_UNIQUE_NAME.DS);

        define('STATIC_PAGE_PATH', 'estaticas');

        // External server or a local dir
        define('MEDIA_DIR', INSTANCE_UNIQUE_NAME);
        // Full path to the instance media files
        define('MEDIA_DIR_URL', MEDIA_URL.SS.MEDIA_DIR.SS);

        // local path to write media (/path/to/media)
        define('MEDIA_PATH', SITE_PATH."media".DS.INSTANCE_UNIQUE_NAME);
        define('IMG_DIR', "images");
        define('FILE_DIR', "files");
        define('ADS_DIR', "advertisements");
        define('OPINION_DIR', "opinions");

        define('MEDIA_IMG_PATH_URL', MEDIA_URL.SS.MEDIA_DIR.SS.IMG_DIR);
        // TODO: A Eliminar
        // TODO: delete from application
        define('MEDIA_IMG_PATH', MEDIA_PATH.DS.IMG_DIR);
        // TODO: delete from application
        define('MEDIA_IMG_PATH_WEB', MEDIA_URL.SS.MEDIA_DIR.SS.IMG_DIR);

        /**
        * Template settings
        **/
        define('TEMPLATE_USER_PATH', SITE_PATH.DS."themes".DS.TEMPLATE_USER.DS);
        define('TEMPLATE_USER_URL', SITE_URL."themes".SS.TEMPLATE_USER.SS);

        define('TEMPLATE_ADMIN', "default");
        define('TEMPLATE_ADMIN_PATH',
                SITE_PATH.DS.ADMIN_DIR.DS."themes".DS.TEMPLATE_ADMIN);
        define('TEMPLATE_ADMIN_PATH_WEB',
                SS.ADMIN_DIR.SS."themes".SS.TEMPLATE_ADMIN.SS);
        define('TEMPLATE_ADMIN_URL',
                SITE_URL_ADMIN.SS."themes".SS.TEMPLATE_ADMIN.SS);
        define('ADVERTISEMENT_ENABLE', true);


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

    /**
     * Sets the PHP environment given an environmen
     * name 'production', 'development'
     *
     * @return void
     **/
    public static function initEnvironment($environment = 'production')
    {
        if ($environment == 'development') {
            ini_set('expose_php', 'On');
            ini_set('error_reporting', E_ALL | E_STRICT);
            ini_set('display_errors', 'On');
            ini_set('display_startup_errors', 'On');
            ini_set('html_errors', 'On');
        } else {
            ini_set('expose_php', 'off');
        }
        ini_set('apc.slam_defense', '0');
    }

    // TODO: move to a separated file called functions.php
    /**
     * Returns the available languages
     *
     * @return array the list of languages
     **/
    public static function getAvailableLanguages()
    {
        return array(
            'en_US' => "English",
            'es_ES' => "Español",
            'gl_ES' => "Galego"
        );
    }

    /**
    * This function retrieves the logger instance that is in the Zend registry
    *
    * @return An instance of Onm logger
    */
    public static function getLogger()
    {
        return \Zend_Registry::get('logger');
    }


    /* Events system */
    public function register($event, $callback, $args=array())
    {
        $this->events[$event][] = array($callback, $args);
    }

    public function dispatch($eventName, $instance, $args=array())
    {
        if (isset($this->events[$eventName])) {
            $events = $this->events[$eventName];

            if (is_array($events)) {
                foreach ($events as $event) {
                    $callback = $event[0];
                    $args     = array_merge($args, $event[1]);

                    if (is_object($instance)) {
                        if (method_exists($instance, $callback)) {
                            // Call to the instance
                            call_user_func_array(array(&$instance, $callback),
                                $args);
                        }
                    } else {
                        // Static call
                        call_user_func_array(array($instance, $callback),
                            $args);
                    }
                }
            }
        }
    }

    // TODO: move to a separated file called functions.php
    /**
    * Raise an HTTP redirection to given url
    *
    * Use the header PHP function to redirect browser to another page
    *
    * @param string $url the url to redirect to
    */
    public static function forward($url)
    {
        header("Location: ".$url);
        exit(0);
    }

    // TODO: move to a separated file called functions.php
    // TODO: rename the function to isMobile()
    /**
     * Detect a mobile device and redirect to mobile version
     *
     * @param  boolean $autoRedirect
     * @return boolean True if it's a mobile device and $autoRedirect is false
     */
    public function mobileRouter($autoRedirect = true)
    {
        $isMobileDevice = false;
        $showDesktop = filter_input(INPUT_GET, 'show_desktop', FILTER_DEFAULT);
        if ($showDesktop) {
            $autoRedirect = false;
            $_COOKIE['confirm_mobile'] = 1;
        }

        // Browscap library
        require APPLICATION_PATH .DS.'vendor'.DS.'Browscap.php';

        // Creates a new Browscap object (loads or creates the cache)
        $bc = new Browscap( APPLICATION_PATH .DS.'tmp'.DS.'cache');
        $browser = $bc->getBrowser(); //isBanned

        if (
            !empty($browser->isMobileDevice)
            && ($browser->isMobileDevice == true)
            && !(isset($_COOKIE['confirm_mobile']))
        ) {
            if ($autoRedirect) {
                Application::forward('/mobile' . $_SERVER['REQUEST_URI']);
            } else {
                $isMobileDevice = true;
            }
        }

        return $isMobileDevice;
    }

    // TODO: move to a separated file called functions.php
    /**
     * Check if current request is from backend
     *
     * Checks if the current URI requrested belongs to admin panel
     *
     * @return boolean true if request is from backend
    */
    public static function isBackend()
    {
        return strncasecmp($_SERVER['REQUEST_URI'], '/admin/', 7) == 0 ;
    }

    /**
    * Perform a permanently redirection (301)
    *
    * Use the header PHP function to redirect browser to another page
    *
    * @param string $url the url to redirect to
    */
    public static function forward301($url)
    {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url);
        exit(0);
    }

    // TODO: move to a separated file called functions.php
    /**
    * Wrapper to output content to AJAX requests
    *
    * @param string $htmlout, the content to output
    * @return null
    */
    public static function ajaxOut($htmlout)
    {
        header("Cache-Control: no-cache");
        header("Pragma: nocache");
        echo $htmlout;
        exit(0);
    }

    // TODO: move to a separated file called functions.php
    /**
    * Stablishes a cookie value in a secure way
    */
    public static function setCookieSecure(
        $name,
        $value,
        $expires =0,
        $domain  ='/'
    ) {
        setcookie($name, $value, $expires, $domain,
            $_SERVER['SERVER_NAME'], isset($_SERVER['HTTPS']), true);
    }

    // TODO: move to a separated file called functions.php
    /**
     * Try to get the real IP of the client
     *
     * @return string the client ip
     **/
    public static function getRealIP()
    {
        // REMOTE_ADDR: dirección ip del cliente
        // HTTP_X_FORWARDED_FOR: si no está vacío indica que se ha utilizado
        // un proxy. Al pasar por el proxy lo que hace este es poner su
        // dirección IP como REMOTE_ADDR y añadir la que estaba como
        // REMOTE_ADDR al final de esta cabecera.
        // En el caso de que la petición pase por varios proxys cada uno
        // repite la operación, por lo que tendremos una lista de direcciones
        // IP que partiendo del REMOTE_ADDR original irá indicando los proxys
        // por los que ha pasado.

        if (
            isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            && $_SERVER['HTTP_X_FORWARDED_FOR'] != ''
        ) {
            $clientIp = ( !empty($_SERVER['REMOTE_ADDR']) ) ?
                $_SERVER['REMOTE_ADDR']
                :
                ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
                    $_ENV['REMOTE_ADDR']
                    :
                    "unknown" );

            // los proxys van añadiendo al final de esta cabecera
            // las direcciones ip que van "ocultando". Para localizar la ip real
            // del usuario se comienza a mirar por el principio hasta encontrar
            // una dirección ip que no sea del rango privado. En caso de no
            // encontrarse ninguna se toma como valor el REMOTE_ADDR

            $entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);

            reset($entries);
            while (list(, $entry) = each($entries)) {
                $entry = trim($entry);
                $foundRegExp = preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/",
                    $entry, $ipList);
                if ($foundRegExp) {
                    // http://www.faqs.org/rfcs/rfc1918.html
                    $privateIp = array(
                          '/^0\./',
                          '/^127\.0\.0\.1/',
                          '/^192\.168\..*/',
                          '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
                          '/^10\..*/');

                    $foundIP = preg_replace($privateIp, $clientIp, $ipList[1]);

                    if ($clientIp != $foundIP) {
                        $clientIp = $foundIP;
                        break;
                    }
                }
            }
        } else {
            $clientIp = ( !empty($_SERVER['REMOTE_ADDR']) ) ?
                $_SERVER['REMOTE_ADDR']
                :
                ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
                    $_ENV['REMOTE_ADDR']
                    :
                    "unknown" );
        }

        return $clientIp;
    }

    // TODO: move to a separated file called functions.php
    /**
     * Register in the log one event in the content
     *
     * @return void
     * @author
     **/
    public static function logContentEvent($action=null, $content=null)
    {
        $logger = Application::getLogger();

        $msg = 'User '.$_SESSION['username'].'(ID:'.$_SESSION['userid']
            .') has executed the action '.$action;
        if (!empty($content)) {
            $msg.=' at '.get_class($content).' (ID:'.$content->id.')';
        }

        $logger->notice($msg);
    }

    // TODO: move to a separated file called functions.php
    /**
     * Register in the Database error handler one error message
     *
     * @return boolean true if all was sucessfully performed
     * @author
     **/
    public static function logDatabaseError()
    {
        $errorMsg = $GLOBALS['application']->conn->ErrorMsg();

        $logger = Application::getLogger();
        $logger->notice('[Database Error] '.$errorMsg, 'normal');

        $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
        $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

        return $errorMsg;
    }

}
