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
            $GLOBALS['application'] = new \Application;
        }

        return $GLOBALS['application'];
    }

    /**
     * Initializes the database connection
     *
     * @return void
     **/
    public static function initDatabase($dbConn)
    {
        $GLOBALS['application']->conn = $dbConn;
    }
}
