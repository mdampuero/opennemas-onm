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
    var $conn           = null;
    var $logger         = null;
    var $workflow       = null;
    var $errors         = array();
    var $adodb          = null;
    var $smarty         = null;
    var $log            = null;
    var $menu           = null;
    var $pager          = null;
    var $template       = null;
    var $sesion         = null;
    var $cache          = null;
    var $image          = null;
    var $events         = array();
    static $request        = null;

    /**
     * Initializes the Application class.
     **/
    public function __construct()
    {
        $this->adodb        = SITE_LIBS_PATH.'adodb5/adodb.inc.php';
        $this->smarty       = SITE_LIBS_PATH.'smarty/Smarty.class.php';
        $this->log          = SITE_LIBS_PATH.'Log.php';
        $this->pager        = SITE_LIBS_PATH.'Pager/Pager.php';
        $this->template     = SITE_LIBS_PATH.'template.class.php';
    }


    /**
    * Setup the Application instance and assigns it to a global variable
    *
    * If global variable application doesn't exists create an instance of it,
    * and setup up DB conection, Adodb logger instance, Workflow
    * logger instance,
    *
    * @access static
    * @return object $GLOBALS['application']
    */
    static public function load()
    {
        if (!isset($GLOBALS['application']) || $GLOBALS['application']==NULL) {


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

        return( $GLOBALS['application'] );
    }

    /*
     * Initializes the Request object and register it inside Application object
     *
     * @param $
     */
    static public function getRequest()
    {
        return \Onm\Request::getInstance();
    }

    static public function initLogger()
    {
        // init Logger
        $logLevel = (s::get('log_level'))?: 'normal';
        $logger = new \Onm\Log($logLevel);
        $registry = Zend_Registry::set('logger', $logger);

        // Composite Logger (file + mail)
        // http://www.indelible.org/php/Log/guide.html#composite-handlers
        if ( s::get('log_enabled') == 1) {
            $GLOBALS['application']->logger = \Log::singleton('composite');

            $conf = array('mode' => 0600,
                          'timeFormat' => '[%Y-%m-%d %H:%M:%S]',
                          'lineFormat' => '%1$s %2$s [%3$s] %4$s %5$s %6$s');
            $fileLogger = &Log::singleton(
                'file', SYS_LOG_FILENAME, 'application', $conf
            );
            $GLOBALS['application']->logger->addChild($fileLogger);
        } else {
            $GLOBALS['application']->logger = \Log::singleton('null');
        }
    }

    static public function initDatabase()
    {

    /*     // Database
        self::$conn = \ADONewConnection(BD_TYPE);
        self::$conn->Connect(
            BD_HOST, BD_USER, BD_PASS, BD_DATABASE
        );

        // Check if adodb log is enabled
        if (s::get('log_db_enabled') == 1) {
            self::$conn->LogSQL();
        }
     *
     */
        // Database
        $GLOBALS['application']->conn = \ADONewConnection(BD_TYPE);
        $GLOBALS['application']->conn->Connect(
            BD_HOST, BD_USER, BD_PASS, BD_DATABASE
        );

        // Check if adodb is log enabled
        if (s::get('log_db_enabled') == 1) {
            $GLOBALS['application']->conn->LogSQL();
        }
    }

    static public function getConnection($data = array())
    {
        if (self::$conn == null || !(self::$conn instanceof \ADOConnection)) {
            // Database
            self::$conn = \ADONewConnection($data['BD_TYPE']);
            self::$conn->Connect(
               $data[' BD_HOST'], $data['BD_USER'], $data['BD_PASS'], $data['BD_DATABASE']
            );

            // Check if adodb log is enabled
            if (s::get('log_db_enabled') == 1) {
                self::$conn->LogSQL();
            }
        }
        return self::$conn;
    }

    static public function setConnection($connectionObject)
    {
        if ($connectionObject instanceof \ADOConnection) {
            throw new \Exception('$connectionObject is not an instance of ADOConnection');
        }
        self::$conn = $connectionObject;
        return self::$conn;
    }

    /**
     * Set up gettext translations.
     */
    static public function initL10nSystem()
    {
        $timezone = s::get('time_zone');
        if (isset($timezone)) {
            $availableTimezones = \DateTimeZone::listIdentifiers();
            date_default_timezone_set($availableTimezones[$timezone]);
        }

        /* Set internal character encoding to UTF-8 */
        mb_internal_encoding("UTF-8");

        $locale = s::get('site_language'). ".UTF-8";
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
     * @author
     **/
    static public function initTimeZone()
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
    static public function initAutoloader($packages=null)
    {
        // Instanciate Zend_Loader_Autoloader
        require_once 'Zend/Loader/Autoloader.php';
        $autoloader = Zend_Loader_Autoloader::getInstance();
        // Register Onm_ Namespace
        $autoloader->registerNamespace('Onm_');

        $libs = array(  'adodb'    => SITE_VENDOR_PATH.'/adodb5/adodb.inc.php',
                        'pager'    => SITE_VENDOR_PATH.'/Pager/Pager.php',
                        'template' => array(
                                        SITE_VENDOR_PATH.'/smarty/smarty-legacy/Smarty.class.php',
                                        SITE_VENDOR_PATH.'/Log.php',
                                        SITE_VENDOR_PATH.'/Template.php'
                                    ),
                     );

        // if no packages was given load the common libraries
        if ( is_null($packages) || $packages == '*' ) {
            foreach ($libs as $lib) {
                if ( !is_array($lib) ) {
                    require_once($lib);
                } else {
                    foreach ($lib as $dependencia) {
                        require_once($dependencia);
                    }
                }
            }
        // if packages was given as argument try to merge with common libraries
        // and load all of them
        } else {
            $pcks = explode(';', $packages);
            foreach ($pcks as $p) {
                if ( array_key_exists($p, $libs) ) {
                    if ( !is_array($libs[$p]) ) {
                        require_once($libs[$p]);
                    } else {
                        foreach ($libs[$p] as $dependencia) {
                            require_once($dependencia);
                        }
                    }
                }
            }
        }

        // Function to autoload classes on the fly using SPL autload
        spl_autoload_register('Application::autoload');
    }

    /**
     * Autoloads classes by its name.
     *
     * @param string $className the name of the class.
     *
     * @return boolean true if class was found and loaded
     */
    static private function autoload($className)
    {

        // Use Onm old loader
        $filename = strtolower($className);
        if ( file_exists(dirname(__FILE__).'/'.$filename.'.class.php') ) {
            require dirname(__FILE__).'/'.$filename.'.class.php';
            return true;
        } elseif ( file_exists(SITE_MODELS_PATH.'/'.$filename.'.class.php') ) {
            require SITE_MODELS_PATH.'/'.$filename.'.class.php';
            return true;
        } else {
            // Try convert MethodCacheManager to method_cache_manager
            $filename = strtolower(
                preg_replace('/([a-z])([A-Z])/', '$1_$2', $className)
            );

            if ( file_exists(dirname(__FILE__).'/'.$filename.'.class.php') ) {
                require dirname(__FILE__).'/'.$filename.'.class.php';
                return true;
            } elseif ( file_exists(SITE_MODELS_PATH.'/'.$filename.'.class.php') ) {
                require SITE_MODELS_PATH.'/'.$filename.'.class.php';
                return true;
            }
        }

        // Use PSR-0 Final Proposal autoloader
        if (strripos($className, '\\') !== false) {
            $className = ltrim($className, '\\');
            $fileName  = '';
            $namespace = '';
            if ($lastNsPos = strripos($className, '\\')) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName  = str_replace(
                    '\\',
                    DIRECTORY_SEPARATOR,
                    $namespace
                ) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

            require $fileName;
        }

    }


    /*
     * Initializes all the internal application constans
     *
     */
    static public function initInternalConstants()
    {
        /**
         * System setup
         **/
        define('STATUS', "1");
        define('CHARSET', "text/html; charset=UTF-8");

        $protocol = (!empty($_SERVER['HTTPS']))? 'https://': 'http://';

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
        $cachepath = APPLICATION_PATH.DS.'tmp'.DS.'instances'.DS.INSTANCE_UNIQUE_NAME;
        if (!file_exists($cachepath)) { mkdir($cachepath, 0755, true); }
        define('CACHE_PATH', realpath($cachepath));

        /**
         * Logging settings
         **/
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('SYS_LOG_FILENAME', SYS_LOG_PATH.DS.'application.log');
        define('SYS_SESSION_PATH', $cachepath.DS."/sessions".DS);
        define('OPENNEMAS_BACKEND_SESSIONS', SYS_SESSION_PATH.'backend/');
        define('OPENNEMAS_FRONTEND_SESSIONS', SYS_SESSION_PATH.'frontend/');
        if (!file_exists(SYS_SESSION_PATH) ) { mkdir(SYS_SESSION_PATH); }
        if (!file_exists(OPENNEMAS_BACKEND_SESSIONS) ) { mkdir(OPENNEMAS_BACKEND_SESSIONS); }
        if (!file_exists(OPENNEMAS_FRONTEND_SESSIONS)) { mkdir(OPENNEMAS_FRONTEND_SESSIONS); }
        define('SYS_NAME_GROUP_ADMIN', 'Administrador'); // TODO: delete from application

        /**
         * Media paths and urls configurations
         **/

        //TODO: All the MEDIA_* should be ported to use this constant
        define('INSTANCE_MEDIA', MEDIA_URL.INSTANCE_UNIQUE_NAME.DS);
        define('INSTANCE_MEDIA_PATH', SITE_PATH.DS."media".DS.INSTANCE_UNIQUE_NAME.DS);


        define('MEDIA_DIR', INSTANCE_UNIQUE_NAME);    // External server or a local dir
        define('MEDIA_DIR_URL', MEDIA_URL.SS.MEDIA_DIR.SS); // Full path to the instance media files

        define('MEDIA_PATH', SITE_PATH."media".DS.INSTANCE_UNIQUE_NAME); // local path to write media (/path/to/media)
        define('IMG_DIR', "images");
        define('FILE_DIR', "files");
        define('ADS_DIR', "advertisements");
        define('OPINION_DIR', "opinions");

        define('MEDIA_IMG_PATH_URL', MEDIA_URL.SS.MEDIA_DIR.SS.IMG_DIR);
        // TODO: A Eliminar
        define('MEDIA_IMG_PATH', MEDIA_PATH.DS.IMG_DIR); // TODO: delete from application
        define('MEDIA_IMG_PATH_WEB', MEDIA_URL.SS.MEDIA_DIR.SS.IMG_DIR); // TODO: delete from application

        /**
        * Template settings
        **/
        define('TEMPLATE_USER_PATH',     SITE_PATH.DS."themes".DS.TEMPLATE_USER.DS);
        define('TEMPLATE_USER_URL', SITE_URL."themes".SS.TEMPLATE_USER.SS);

        define('TEMPLATE_ADMIN', "default");
        define('TEMPLATE_ADMIN_PATH',SITE_PATH.DS.ADMIN_DIR.DS."themes".DS.TEMPLATE_ADMIN);
        define('TEMPLATE_ADMIN_PATH_WEB',SS.ADMIN_DIR.SS."themes".SS.TEMPLATE_ADMIN.SS);
        define('TEMPLATE_ADMIN_URL', SITE_URL_ADMIN.SS."themes".SS.TEMPLATE_ADMIN.SS);
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
        $GLOBALS['USER_ID'] = NULL;
        $GLOBALS['conn'] = NULL;

        define('ITEMS_PAGE', "20"); // TODO: delete from application
    }

    /**
    * This function retrieves the logger instance that is in the Zend registry
    *
    * @return An instance of Onm logger
    */
    static public function getLogger()
    {
        return \Zend_Registry::get('logger');
    }

    /**
    * Raise an HTTP redirection to given url
    *
    * Use the header PHP function to redirect browser to another page
    *
    * @param string $url the url to redirect to
    */
    static public function forward($url)
    {
        header("Location: ".$url);
        exit(0);
    }

    /**
     * Detect a mobile device and redirect to mobile version
     *
     * @param boolean $autoRedirect
     * @return boolean True if it's a mobile device and $autoRedirect is false
     */
    public function mobileRouter($autoRedirect=true)
    {
        $isMobileDevice = false;
        $showDesktop = filter_input(INPUT_GET,'show_desktop',FILTER_DEFAULT);
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
                Application::forward('/mobile' . $_SERVER['REQUEST_URI'] );
            } else {
                $isMobileDevice = true;
            }
        }

        return $isMobileDevice;
    }

    /**
     * Check if current request is from backend
     *
     * Checks if the current URI requrested belongs to admin panel
     *
     * @return boolean true if request is from backend
    */
    static public function isBackend()
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
    static public  function forward301($url)
    {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url);
        exit(0);
    }

    // Redirección sobre el frame principal
    static public function forwardTargetParent($url)
    {
        $html =<<<HTMLCODE
<html>
<head>
  <meta http-equiv="refresh" content="0;url=/admin/login.php" />
</head>
<body>
<script> window.top.location="$url";</script>
</body>
</html>
HTMLCODE;
        echo($html);

        exit(0);
    }

    /**
    * Wrapper to output content to AJAX requests
    *
    *
    * @access static
    * @param string $htmlout, the content to output
    * @return null
    */
    static public function ajax_out($htmlout)
    {
        header("Cache-Control: no-cache");
        header("Pragma: nocache");
        echo $htmlout;
        exit(0);
    }

    /* Events system */
    public function register($event, $callback, $args=array())
    {
        $this->events[$event][] = array($callback, $args);
    }

    public function dispatch($eventName, $instance, $args=array())
    {
        if ( isset($this->events[$eventName]) ) {
            $events = $this->events[$eventName];

            if ( is_array($events) ) {
                foreach ($events as $i => $event) {
                    $callback = $event[0];
                    $args     = array_merge($args, $event[1]);

                    if (is_object($instance)) {
                        if (method_exists($instance, $callback)) {
                            // Call to the instance
                            call_user_func_array(
                                array(&$instance, $callback), $args
                            );
                        }
                    } else {
                        // Static call
                        call_user_func_array(
                            array($instance, $callback), $args
                        );
                    }
                }
            }
        }
    }

    /**
    * Stablishes a cookie value in a secure way
    *
    * @access public
    * @param bool,string,integer,double $baz
    * @return mixed
    * @author nameofauthor
    * Other available tags: @tutorial, @version, @copyright, @deprecated,
    * @example, @ignore, @link, @see, @since
    */
    public function setcookie_secure($name, $value, $expires=0, $domain='/')
    {
        setcookie(
            $name, $value, $expires, $domain,
            $_SERVER['SERVER_NAME'], isset($_SERVER['HTTPS']), true
        );
    }

    /**
    * Try to get the real IP of the client
    *
    * @access static
    * @return string, the client ip
    */
    static public function getRealIP()
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
                if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ipList) ) {
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

    /**
     * Register in the log one event in the content
     *
     * @return void
     * @author
     **/
    static public function logContentEvent($action, $content)
    {
        $logger = Application::getLogger();
        $logger->notice(
            'User '.$_SESSION['username'].'(ID:'.$_SESSION['userid'].') has executed '
            .'the action '.$action.' at '.get_class($content).' (ID:'.$content->id.')' );
    }

    /**
     * Register in the Database error handler one error message
     *
     * @return boolean true if all was sucessfully performed
     * @author
     **/
    static public function logDatabaseError()
    {
        $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
        $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
        $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
        return $errorMsg;
    }

}

/* Others commons functions */
if (!function_exists('clearslash')) {
    function clearslash($string)
    {
        $string = stripslashes($string);
        $string = str_replace("\\", '', $string);

        return stripslashes($string);
    }
}
