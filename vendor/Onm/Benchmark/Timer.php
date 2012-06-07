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
 * $timer = new \Onm\Benchmark\Timer();
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
    public $markers = null;

    /*
     * Initilizes the object
     *
     */
    public function __construct()
    {
        $this->markers = array();
    }

    /**
     * Explanation for this function.
     *
     * @param string $marker The marker where we want to start the count.
     */
    public function start($marker = "default")
    {
        $this->markers[$marker]['starttime'] = $this->_getMicrotime();
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
        if (array_key_exists($marker, $this->markers)) {
            $this->markers[$marker]['endtime'] = $this->_getMicrotime();
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
                $this->markers[$marker]['endtime'],
                $this->markers[$marker]['starttime'],
                6
            );
        } else {
            return $this->markers[$marker]['endtime']
                    - $this->markers[$marker]['starttime'];
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
