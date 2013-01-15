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
 **/
class Application
{
    public $conn           = null;
    public $logger         = null;
    public $errors         = array();
    public $adodb          = null;
    public $smarty         = null;
    public $log            = null;
    public $cache          = null;
    public $events         = array();
    public static $language = null;

    /**
    * Setup the Application instance and assigns it to a global variable
    *
    * @return object $GLOBALS['application']
    */
    public static function load()
    {
        self::initEnvironment(ENVIRONMENT);

        if (!isset($GLOBALS['application']) || $GLOBALS['application']==null) {
            // Setting up static Constants

            $GLOBALS['application'] = new Application();

            if (INSTANCE_UNIQUE_NAME != 'onm_manager') {
                // Setting up DataBase connection
                self::initDatabase();

                // Setting up Logger
                self::initLogger();

                self::initTimeZone();
            }
        }

        return $GLOBALS['application'];
    }

    public static function initDatabase()
    {
        // Databasew
        $GLOBALS['application']->conn = \ADONewConnection(BD_TYPE);
        $GLOBALS['application']->conn->Connect(BD_HOST, BD_USER, BD_PASS, BD_DATABASE);

        $GLOBALS['application']->conn->bulkBind = true;

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
        if ( s::get('log_enabled') == 'on') {
            $GLOBALS['application']->logger = \Log::singleton('composite');

            $conf = array('mode' => 0600,
                          'timeFormat' => '[%Y-%m-%d %H:%M:%S]',
                          'lineFormat' => '%1$s %2$s [%3$s] %4$s %5$s %6$s');
            $fileLogger = &Log::singleton(
                'file',
                SYS_LOG_FILENAME,
                'application',
                $conf
            );
            $GLOBALS['application']->logger->addChild($fileLogger);
        } else {
            $GLOBALS['application']->logger = \Log::singleton('null');
        }
    }

    /**
     * Sets the timezone for this app from the instance settings
     *
     * @return void
     **/
    public static function initTimeZone()
    {
        $timezone = s::get('time_zone');
        if (isset($timezone)) {
            $availableTimezones = \DateTimeZone::listIdentifiers();
            date_default_timezone_set($availableTimezones[$timezone]);
        }
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
    public function register($event, $callback, $args = array())
    {
        $this->events[$event][] = array($callback, $args);
    }

    public function dispatch($eventName, $instance, $args = array())
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
        $bc = new \Browscap(APPLICATION_PATH .DS.'tmp'.DS.'cache');
        $browser = $bc->getBrowser(); //isBanned

        if (
            !empty($browser->isMobileDevice)
            && ($browser->isMobileDevice == true)
            && !(isset($_COOKIE['confirm_mobile']))
        ) {
            if ($autoRedirect) {
                header("Location: ".'/mobile' . $_SERVER['REQUEST_URI']);
                exit(0);
            } else {
                $isMobileDevice = true;
            }
        }

        return $isMobileDevice;
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

    /**
     * Stablishes a cookie value in a secure way
     */
    public static function setCookieSecure($name, $value, $expires = 0, $domain = '/')
    {
        setcookie($name, $value, $expires, $domain, $_SERVER['SERVER_NAME'], isset($_SERVER['HTTPS']), true);
    }

    // TODO: move to a separated file called functions.php
    /**
     * Try to get the real IP of the client
     *
     * @return string the client ip
     **/
    public static function getRealIp()
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
                $foundRegExp = preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ipList);
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
     **/
    public static function logContentEvent($action = null, $content = null)
    {
        $logger = Application::getLogger();

        $msg = 'User '.$_SESSION['username'].'(ID:'.$_SESSION['userid']
            .') has executed the action '.$action;
        if (!empty($content)) {
            $msg.=' at '.get_class($content).' (ID:'.$content->id.')';
        }
        // var_dump($msg, $logger);die();

        $logger->notice($msg);
    }

    // TODO: move to a separated file called functions.php
    /**
     * Register in the Database error handler one error message
     *
     * @return boolean true if all was sucessfully performed
     **/
    public static function logDatabaseError()
    {
        $errorMsg = $GLOBALS['application']->conn->ErrorMsg();

        $logger = Application::getLogger();
        $logger->notice('[Database Error] '.$errorMsg, 'normal');

        return $errorMsg;
    }
}
