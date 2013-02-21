<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 *
 *
 * @package    Onm
 * @subpackage Log
 */
class Log
{
    private $log = null;

    public function __construct($loglevel = 'normal')
    {
        $logFilePath = SYS_LOG_PATH.'/'.INSTANCE_UNIQUE_NAME.'-onm.log';

        $this->log = new Logger($loglevel);
        $this->log->pushHandler(new StreamHandler($logFilePath, Logger::INFO));
    }


    public function __call($funcName, $arguments)
    {
        $called = call_user_func_array(array(&$this->log, $funcName), $arguments);

        return $called;
    }
}
