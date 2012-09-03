<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm;

use Onm\Settings as s;

/**
 * Explanation for this class.
 *
 * EMERG   = 0;  // Emergency: system is unusable
 * ALERT   = 1;  // Alert: action must be taken immediately
 * CRIT    = 2;  // Critical: critical conditions
 * ERR     = 3;  // Error: error conditions
 * WARN    = 4;  // Warning: warning conditions
 * NOTICE  = 5;  // Notice: normal but significant condition
 * INFO    = 6;  // Informational: informational messages
 * DEBUG   = 7;  // Debug: debug messages
 *
 * @param $loglevel is the level of verbosity of the logger
 *        'normal' -> from 5 to 7
 *        'verbose'-> from 3 to 7
 *        'all'    -> from 0 to 7
 *
 * @package    Onm
 * @subpackage Log
 * @author     Alexandre Rico <alex@openhost.es>
 * @version    GIT: Id:  Mar Xuñ 28 10:44:51 2011 Alexandre
 */
class Log extends \Zend_Log
{

    public function __construct($loglevel = 'normal')
    {
        // set formatter, add %class% to save class name
        $format = date('Y-m-d H:i:s', time()).' (%priorityName%-%priority%) %message%'
        . PHP_EOL;
        $this->_formatter = new \Zend_Log_Formatter_Simple($format);
        parent::addWriter($this->_errorWriter($loglevel));
        parent::__construct();
    }

    /**
     * Factory to construct the logger and one or more writers
     * based on the configuration array
     *
     * @param  array|Zend_Config Array or instance of Zend_Config
     * @return Zend_Log
     * @throws Zend_Log_Exception
     */
    public static function factory($config = array())
    {
        if ($config instanceof \Zend_Config) {
            $config = $config->toArray();
        }

        if (!is_array($config) || empty($config)) {
            /** @see Zend_Log_Exception */
            require_once 'Zend/Log/Exception.php';
            throw new \Zend_Log_Exception('Configuration must be an array or instance of Zend_Config');
        }

        $log = new static;

        if (array_key_exists('timestampFormat', $config)) {
            if (null != $config['timestampFormat'] && '' != $config['timestampFormat']) {
                $log->setTimestampFormat($config['timestampFormat']);
            }
            unset($config['timestampFormat']);
        }

        if (!is_array(current($config))) {
            $log->addWriter(current($config));
        } else {
            foreach ($config as $writer) {
                $log->addWriter($writer);
            }
        }

        return $log;
    }

    /**
    * Writer for error log message
    * Error log message will write to file error.log
    * This will only log messages of level <= 3
    */
    protected function _errorWriter($loglevel)
    {
        if (s::get('log_db_enabled') == 1) {
            $writer = new \Zend_Log_Writer_Stream(SYS_LOG_PATH.'/onm.log');
            switch ($loglevel) {
                case 'normal':
                    $writer->addFilter(new \Zend_Log_Filter_Priority(\Zend_Log::NOTICE, '>='));
                    break;
                case 'verbose':
                    $writer->addFilter(new \Zend_Log_Filter_Priority(\Zend_Log::ERR, '>='));
                    break;
                case 'all':
                    $writer->addFilter(new \Zend_Log_Filter_Priority(\Zend_Log::EMERG, '>='));
                    break;
            }
        } else {
            $writer = new \Zend_Log_Writer_Mock;
        }
        $writer->setFormatter($this->_formatter);

        return $writer;
    }
}

