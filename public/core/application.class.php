<?php
// Prevent direct access
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
    var $activerecord   = null;
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

    //function Application() {
    //    $this->adodb        = SITE_LIBS_PATH.'adodb5/adodb.inc.php';
    //    $this->smarty       = SITE_LIBS_PATH.'smarty/Smarty.class.php';
    //    $this->log          = SITE_LIBS_PATH.'Log.php';
    //    $this->pager        = SITE_LIBS_PATH.'Pager/Pager.php';
    //    $this->template     = SITE_LIBS_PATH.'template.class.php';
    //}

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


    static private function autoload($className) {
        $filename = strtolower($className);
        if( file_exists(dirname(__FILE__).'/'.$filename.'.class.php') ) {
            require dirname(__FILE__).'/'.$filename.'.class.php';

        } else{

            // Try convert MethodCacheManager to method_cache_manager
            $filename = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $className));

            if( file_exists(dirname(__FILE__).'/'.$filename.'.class.php') ) {
                require dirname(__FILE__).'/'.$filename.'.class.php';
            }
        }


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

            // Database
            $GLOBALS['application']->conn = ADONewConnection(BD_TYPE);
            $GLOBALS['application']->conn->Connect(BD_HOST, BD_USER, BD_PASS, BD_INST);

            // Check if adodb is log enabled
            if( defined('ADODB_LOG_ENABLE') && (ADODB_LOG_ENABLE == 1) ) {
                $GLOBALS['application']->conn->LogSQL();
            }

            //$GLOBALS['application']->conn->fnExecute = 'MonitorContentStatus';
            //$GLOBALS['application']->conn->fnExecute = 'CountExecs';

            $conf = array('mode' => 0600,'timeFormat' => '%Y%m%d%H%M%S','lineFormat' => '%1$s [%2$s] %4$s');
            $GLOBALS['application']->workflow = Log::factory('file', SYS_LOG_PATH.'/workflow.log', 'WF', $conf);
            //$GLOBALS['application']->mutex = Log::factory('file', SYS_LOG_PATH.'/mutex.log', 'MUTEX', $conf);

            // Composite Logger (file + mail)
            // http://www.indelible.org/php/Log/guide.html#composite-handlers
            if( defined('LOG_ENABLE') && (LOG_ENABLE == 1)) {
                $GLOBALS['application']->logger = &Log::singleton('composite');

                $conf = array('mode' => 0600,
                              'timeFormat' => '%Y%m%d%H%M%S',
                              'lineFormat' => '%1$s %2$s [%3$s] %4$s %5$s %6$s');
                $fileLogger = &Log::singleton('file', SYS_LOG_FILENAME, 'application', $conf);
                $GLOBALS['application']->logger->addChild($fileLogger);

                /* if(defined('SYS_LOG_EMAIL')) {
                    $conf   = array('subject' => '[LOG] OpenNeMas application logger',
                                    'timeFormat' => '%Y%m%d%H%M%S',
                                    'lineFormat' => '%1$s %2$s [%3$s] %4$s %5$s %6$s');
                    $mailLogger = &Log::singleton('mail', SYS_LOG_EMAIL, 'application', $conf);
                    $GLOBALS['application']->logger->addChild($mailLogger);
                } */
            } else {
                $GLOBALS['application']->logger = &Log::singleton('null');
            }
        }

        return( $GLOBALS['application'] );
    }

    /**
    * Loads all the common libraries and the packages passed as argument
    *
    * Description
    *
    * @access public,static,private,protected
    * @param bool,string,integer,double $baz
    * @return mixed
    * @author nameofauthor
    * Other available tags: @tutorial, @version, @copyright, @deprecated,
    * @example, @ignore, @link, @see, @since
    */
    static function import_libs($packages=null) {
        $libs = array(  'adodb'    => array(SITE_LIBS_PATH.'/adodb5/adodb.inc.php'),
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
    * Raise an HTTP redirection to given url
    *
    * Use the header PHP function to redirect browser to another page
    *
    * @access static
    * @param string $url
    * @return null
    */
    static function forward($url) {
        header ("Location: ".$url);
        exit(0);
    }

    /**
    * If MUTEX_ENABLE is enable try to block the semaphore on a given key
    *
    * Blocks the access to a given key using a semaphore
    *
    * @access public
    * @param string $id, Cache Id to generate sem_id identifier
    * @return null
    */
    public static function getMutex($id)
    {
        if(defined('MUTEX_ENABLE') && MUTEX_ENABLE != 0) {
            $sem_key = crc32($id);
            Application::$sem = sem_get($sem_key, 1, 0666, true);
            sem_acquire(Application::$sem);
            // $GLOBALS['application']->mutex->log('< I (' . $id . '): ' . getmypid());
        }
    }

    /**
    * If MUTEX_ENABLE is enable try to release the semaphore on a given key
    *
    * Release the access to a given key using a semaphore
    *
    * @access public
    * @param string $id, Cache Id to generate sem_id identifier
    * @return null
    */
    public static function releaseMutex()
    {
        if(!is_null(Application::$sem)) {
            // $GLOBALS['application']->mutex->log('> O: ' . getmypid());
            sem_release(Application::$sem);
        }
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
     * @return boolean
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
    * @access static
    * @param string $url
    * @return null
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
