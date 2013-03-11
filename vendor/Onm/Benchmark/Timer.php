<?php
/**
 * Defines the Onm\Benchmark\Timer class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_Benchmark
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
 * @package    Onm_Benchmark
 */
class Timer
{
    /**
     * List of markers
     *
     * @var array
     **/
    public $markers = null;

    /**
     * Initilizes the object
     */
    public function __construct()
    {
        $this->markers = array();
    }

    /**
     * Explanation for this function.
     *
     * @param string $marker The marker where we want to start the count.
     *
     * @return void
     */
    public function start($marker = "default")
    {
        $this->markers[$marker]['starttime'] = $this->getMicrotime();
    }

    /**
     * Stops the time for a given marker.
     *
     * @param string $marker the marker where we want to stop the count.
     *
     * @throws <b>Exception</b> if the marker doesn't exists.
     */
    public function stop($marker = "default")
    {
        if (array_key_exists($marker, $this->markers)) {
            $this->markers[$marker]['endtime'] = $this->getMicrotime();
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
        $starttime = $this->markers[$marker]['endtime'];
        $endtime   = $this->markers[$marker]['starttime'];

        if (extension_loaded('bcmath')) {
            return bcsub($starttime, $endtime, 6);
        } else {
            return $starttime - $endtime;
        }
    }
    /**
     * Wrapper for microtime().
     *
     * @return float
     */
    private function getMicrotime()
    {
        $microtime = explode(' ', microtime());

        return $microtime[1] . substr($microtime[0], 1);
    }
}
