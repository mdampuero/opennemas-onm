<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// Prevent direct access

use \Onm\Settings as s;

if (preg_match('/application\.class\.php/', $_SERVER['PHP_SELF'])) {
    die();
}

function &MonitorContentStatus($db, $sql, $inputarray) {
    if( preg_match('/content_status/', $sql) && preg_match('/^[ ]*update/i', $sql) ) {
        $GLOBALS['application']->workflow->log( 'SQL content_status - ' .
        $_SESSION['username'] . ' - ' . $sql . ' ' . print_r($inputarray, true), PEAR_LOG_INFO );
    }

    $a=null;
    return $a;
}

/**
 * Application
 *
 * @package    Onm
 * @subpackage Application
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: application.class.php 1 2010-10-01 23:21:11Z vifito $
 */
class Application {
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

    /**
     * Semphore to access critic section
     * @var mixed IPC semphore
     */
    public static $sem = null;

    /**
     * Initializer for the Application class
     *
     * @access public
     * @return null
     */
    function __construct() {
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
    * and setup up DB conection, Adodb logger instance, Workflow logger instance,
    *
    * @access static
    * @return object $GLOBALS['application']
    */
    static function load() {
        if(!isset($GLOBALS['application']) || $GLOBALS['application']==NULL) {

            $GLOBALS['application'] = new Application();

            // Setting up DataBase connection
            self::initDatabase();

            // Setting up Logger
            self::initLogger();

            // Setting up Gettext
            self::initGettext();
        }



        return( $GLOBALS['application'] );
    }

    static public function initLogger()
    {
        $logLevel = (s::get('log_level'))?: normal;

        $logger = new \Onm\Log(s::get('log_level'));
        $registry = Zend_Registry::set('logger', $logger);

        // Composite Logger (file + mail)
        // http://www.indelible.org/php/Log/guide.html#composite-handlers
        if( s::get('log_enabled') == 1) {
            $GLOBALS['application']->logger = &Log::singleton('composite');

            $conf = array('mode' => 0600,
                          'timeFormat' => '[%Y-%m-%d %H:%M:%S]',
                          'lineFormat' => '%1$s %2$s [%3$s] %4$s %5$s %6$s');
            $fileLogger = &Log::singleton('file', SYS_LOG_FILENAME, 'application', $conf);
            $GLOBALS['application']->logger->addChild($fileLogger);
        } else {
            $GLOBALS['application']->logger = &Log::singleton('null');
        }
    }

    static public function initDatabase()
    {
        // Database
        $GLOBALS['application']->conn = ADONewConnection(BD_TYPE);
        $GLOBALS['application']->conn->Connect(BD_HOST, BD_USER, BD_PASS, BD_INST);

        // Check if adodb is log enabled
        if(  s::get('log_db_enabled') == 1 ) {
            $GLOBALS['application']->conn->LogSQL();
        }
    }

    /**
     * Set up gettext translations.
     *
     */
    static public function initGettext()
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

        $libs = array(  'adodb'    => SITE_LIBS_PATH.'/adodb5/adodb.inc.php',
                        'log'      => SITE_LIBS_PATH.'/Log.php',
                        'pager'    => SITE_LIBS_PATH.'/Pager/Pager.php',
                        'template' => array(SITE_LIBS_PATH.'/smarty/Smarty.class.php',  SITE_LIBS_PATH.'/template.class.php'),
                     );

        // if no packages was given load the common libraries
        if( is_null($packages) || $packages == '*' ) {
            foreach($libs as $lib) {
                if( !is_array($lib) ) {
                    require_once($lib);
                } else {
                    foreach($lib as $dependencia) {
                        require_once($dependencia);
                    }
                }
            }
        // if packages was given as argument try to merge with common libraries
        // and load all of them
        } else {
            $pcks = explode(';', $packages);
            foreach($pcks as $p) {
                if( array_key_exists($p, $libs) ) {
                    if( !is_array($libs[$p]) ) {
                        require_once($libs[$p]);
                    } else {
                        foreach($libs[$p] as $dependencia) {
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
    static private function autoload($className) {

        // Use Onm old loader
        $filename = strtolower($className);
        if( file_exists(dirname(__FILE__).'/'.$filename.'.class.php') ) {
            require dirname(__FILE__).'/'.$filename.'.class.php';
            return true;
        } else{
            // Try convert MethodCacheManager to method_cache_manager
            $filename = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $className));

            if( file_exists(dirname(__FILE__).'/'.$filename.'.class.php') ) {
                require dirname(__FILE__).'/'.$filename.'.class.php';
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
                $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

            require $fileName;
        }

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
    static function forward($url) {
        header ("Location: ".$url);
        exit(0);
    }


    /**
     * Static function to write the workflow logs
     *
     * @param type $msg the msg to save into log file
     * @return null
     */
    public static function write_log($msg) {
        $time = date('Y-m-d-h:i');
        $GLOBALS['application']->workflow->log( $time.'-'.$_SESSION['userid'].'-'.$_SESSION['username'].'-'.$msg.' \n', PEAR_LOG_INFO );
    }

    /**
     * Detect a mobile device and redirect to mobile version
     *
     * @param boolean $auto_redirect
     * @return boolean True if it's a mobile device and $auto_redirect is false
     */
    function mobileRouter($auto_redirect=true)
    {
        $isMobileDevice = false;

        /*

        // Browscap library
        require dirname(__FILE__) . '/../libs/Browscap.php';

        // Creates a new Browscap object (loads or creates the cache)
        $bc = new Browscap( dirname(__FILE__) . '/../cache');
        $browser = $bc->getBrowser(); //isBanned

        if(!empty($browser->isMobileDevice) && ($browser->isMobileDevice == 1) && !(isset($_COOKIE['confirm_mobile']))) {
            if($auto_redirect) {
                Application::forward('/mobile' . $_SERVER['REQUEST_URI'] );
            } else {
                $isMobileDevice = true;
            }
        }

        */

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
    static function forward301($url)
    {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url);
        exit(0);
    }

    // Redirección sobre el frame principal
    static function forwardTargetParent($url) {
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
    static function ajax_out($htmlout) {
        header("Cache-Control: no-cache");
        header("Pragma: nocache");
        echo $htmlout;
        exit(0);
    }

    /* Events system */
    function register($event, $callback, $args=array()) {
        $this->events[$event][] = array($callback, $args);
    }

    function dispatch($eventName, $instance, $args=array()) {
        if( isset($this->events[$eventName]) ) {
            $events = $this->events[$eventName];

            if( is_array($events) ) {
                foreach($events as $i => $event) {
                    $callback = $event[0];
                    $args     = array_merge($args, $event[1]);

                    if(is_object($instance)) {
                        if(method_exists($instance, $callback)) {
                            // Call to the instance
                            call_user_func_array(array(&$instance, $callback), $args);
                        }
                    } else {
                        // Static call
                        call_user_func_array(array($instance, $callback), $args);
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
    function setcookie_secure($name, $value, $expires=0, $domain='/') {
        setcookie($name, $value, $expires, $domain,
                  $_SERVER['SERVER_NAME'], isset($_SERVER['HTTPS']), true );
    }

    /**
    * Try to get the real IP of the client
    *
    * @access static
    * @return string, the client ip
    */
    static function getRealIP() {
        // REMOTE_ADDR: dirección ip del cliente
        // HTTP_X_FORWARDED_FOR: si no está vacío indica que se ha utilizado un proxy. Al pasar por el proxy lo que hace
        // éste es poner su dirección IP como REMOTE_ADDR y añadir la que estaba como REMOTE_ADDR al final de esta cabecera.
        // En el caso de que la petición pase por varios proxys cada uno repite la operación, por lo que tendremos una lista
        // de direcciones IP que partiendo del REMOTE_ADDR original irá indicando los proxys por los que ha pasado.

        if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '' ) {
            $client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ?
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
                if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list) ) {
                    // http://www.faqs.org/rfcs/rfc1918.html
                    $private_ip = array(
                          '/^0\./',
                          '/^127\.0\.0\.1/',
                          '/^192\.168\..*/',
                          '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
                          '/^10\..*/');

                    $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

                    if ($client_ip != $found_ip)
                    {
                       $client_ip = $found_ip;
                       break;
                    }
                }
            }
        } else {
            $client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ?
                $_SERVER['REMOTE_ADDR']
                :
                ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
                    $_ENV['REMOTE_ADDR']
                    :
                    "unknown" );
        }

        return $client_ip;
    }
}

/* Others commons functions */
if (!function_exists('clearslash')) {
    function clearslash($string) {
        $string = stripslashes($string);
        $string = str_replace("\\", '', $string);

        return stripslashes($string);
    }
}
