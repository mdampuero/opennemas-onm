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
        if (!isset($GLOBALS['application']) || $GLOBALS['application']==null) {
            $GLOBALS['application'] = new Application();

            // Setting up DataBase connection
            self::initDatabase();
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
        $GLOBALS['application']->conn = \ADONewConnection(BD_TYPE);
        $GLOBALS['application']->conn->Connect(BD_HOST, BD_USER, BD_PASS, BD_DATABASE);
        $GLOBALS['application']->conn->bulkBind = true;
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
}
