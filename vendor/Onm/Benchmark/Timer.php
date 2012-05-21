<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Benchmark;
/**
 * Provides timing and profiling information..
 *
 * * Example 1: Starts .
 *
 * <code>
 * <?php
 *
 * $timer = \Onm\Benchmark\Timer::getInstance();
 * $timer->start();
 * $timer->stop();
 * $timer->display();
 * </code>
 *
 *
 * @package    Onm
 * @subpackage Benchmakr
 * @author     Fran Dieguez <fran@openhost.es>
 * @version    SVN: $Id: Benchmark.php 28842 Lun Xuñ 27 11:50:52 2011 frandieguez $
 */
class Timer
{

    static public $instance = null;

    static public $markers = null;


    /**
     * Ensures that we always get one single instance
     *
     * @return  object      the unique instance object
     *
     */
    static public function getInstance($config = array())
    {

        if (!(self::$instance instanceof self)) {
            self::$instance = new self($config);
        }

        return self::$instance;

    }

    /*
     * Initilizes the object
     *
     */
    public function __construct( $config = array())
    {
        if (!isset(self::$markers) && count(self::$markers) < 1) {
            self::$markers = array();
        }
    }

    /**
     * Explanation for this function.
     *
     * @param string $marker The marker where we want to start the count.
     */
    public function start($marker = "default")
    {
        self::$markers[$marker]['starttime'] = $this->_getMicrotime();
    }

    /**
     * Stops the time for a given marker.
     *
     * @param string $marker the marker where we want to stop the count.
     *
     * @throws <b>Exception</b> if the marker doesn't exists.
     */
    public function stop( $marker = "default")
    {
        if (array_key_exists($marker, self::$markers)) {
            self::$markers[$marker]['endtime'] = $this->_getMicrotime();
        } else {
            throw new \Exception("Marker '{$marker}' doesn't exists.");
        }
    }

    /**
     * Fetches the elapsed time between start and end time for a given marker.
     *
     * @param string $marker the marker we want to get elapsed time from.
     *
     * @return int Time in seconds between start time and end time
     */
    public function display($marker = "default")
    {
        if (extension_loaded('bcmath')) {
            return bcsub(
                self::$markers[$marker]['endtime'],
                self::$markers[$marker]['starttime'],
                6
            );
        } else {
            return self::$markers[$marker]['endtime']
                    - self::$markers[$marker]['starttime'];
        }
    }
    /**
     * Wrapper for microtime().
     *
     * @return float
     * @access private
     * @since  1.3.0
     */
    private function _getMicrotime()
    {
        $microtime = explode(' ', microtime());

        return $microtime[1] . substr($microtime[0], 1);
    }
}
