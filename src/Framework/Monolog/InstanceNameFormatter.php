<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\Monolog;

class InstanceNameFormatter
{
    /*
     * Adds the instance unique name to the log record context
     *
     * @param array $record the current log record
     *
     * @return array the modified record
     */
    public function processRecord(array $record)
    {
        $record['extra']['instance'] = INSTANCE_UNIQUE_NAME;

        return $record;
    }
}
