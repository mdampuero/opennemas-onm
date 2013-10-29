<?php
/**
 * Defines the Onm\Log class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_Log
 */
namespace Onm;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;

/**
 * Implements an easy initialization of Monolog service
 *
 * @package    Onm_Log
 */
class Log
{
    /**
     * The log instance
     *
     * @var Monolog\Logger
     **/
    private $log = null;

    /**
     * Initializes the looger instance
     *
     * @param string $loglevel The log name
     *
     * @return void
     **/
    public function __construct($loglevel = 'normal')
    {
        $logFilePath = SYS_LOG_PATH.'/'.INSTANCE_UNIQUE_NAME.'-onm.log';

        $this->log = new Logger($loglevel);
        $this->log->pushHandler(new StreamHandler($logFilePath, Logger::INFO));
    }

    /**
     * The proxy method to redirect all the calls to the logger instance
     *
     * @param string $funcName the function to call inside the logger
     * @param array  $arguments the list of arguments to pass to the function
     *
     * @return mixed the response of the logger method call
     **/
    public function __call($funcName, $arguments)
    {
        $called = call_user_func_array(array(&$this->log, $funcName), $arguments);

        return $called;
    }
}
