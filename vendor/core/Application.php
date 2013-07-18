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
    /**
     * Database connection object
     *
     * @var AdodbConnection
     **/
    public $conn                = null;

    /**
     * Static access to the logger instance
     *
     * @var Monolog
     **/
    public static $loggerStatic = null;

    /**
     * Registered events
     *
     * @var array
     **/
    public $events              = array();

    /**
     * Current application language
     *
     * @var string
     **/
    public static $language     = null;

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

            // Setting up DataBase connection
            self::initDatabase();

            // Setting up Logger
            self::initLogger();
        }

        return $GLOBALS['application'];
    }

    /**
     * Initializes the database connection
     *
     * @return void
     **/
    public static function initDatabase()
    {
        // Database
        $GLOBALS['application']->conn = \ADONewConnection(BD_TYPE);
        $GLOBALS['application']->conn->Connect(BD_HOST, BD_USER, BD_PASS, BD_DATABASE);

        $GLOBALS['application']->conn->bulkBind = true;
        // $GLOBALS['application']->conn->LogSQL();
    }

    /**
     * Initializes the logger instance
     *
     * @return
     **/
    public static function initLogger()
    {
        self::$loggerStatic = new \Onm\Log('normal');
        $GLOBALS['application']->logger = self::$loggerStatic;
    }


    /**
     * Sets the PHP environment given an environmen
     * name 'production', 'development'
     *
     * @param string $environment The current environment
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
        return self::$loggerStatic;
    }


    /**
     * Registers a new event handler for a given event name
     *
     * @param string $event the event name
     * @param string $callback the function to call when firing this event
     * @param string $args the params to pass to the function
     *
     * @return void
     **/
    public function register($event, $callback, $args = array())
    {
        $this->events[$event][] = array($callback, $args);
    }

    /**
     * Fires an event given the event name
     *
     * @param string $eventName the event to fire
     * @param object|string $instance the class to call
     * @param array $args the list of arguments to pass to the callback
     *
     * @return
     **/
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
    /**
     * Registers in the log one event in the content
     *
     * @param string $action the action to log
     * @param string $content the content of the action to log
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

        $logger->notice($msg);
    }

    // TODO: move to a separated file called functions.php
    /**
     * Registers in the Database error handler one error message
     *
     * @return boolean true if all was sucessfully performed
     **/
    public static function logDatabaseError()
    {
        $errorMsg = $GLOBALS['application']->conn->ErrorMsg();

        $logger = Application::getLogger();
        $logger->notice('[Database Error] '.$errorMsg);

        return $errorMsg;
    }
}
