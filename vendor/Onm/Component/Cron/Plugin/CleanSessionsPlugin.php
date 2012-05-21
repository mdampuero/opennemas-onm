<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Component\Cron\Plugin;

use Onm\Component\Cron\Plugin\PluginInterface,
    Onm\Component\Cron\Cron;
/**
 * Cleans the session files
 *
 * @package Onm_Component_Cron_Plugin
 * @author
 **/
class CleanSessions implements PluginInterface
{
    /**
     * Initializes the Plugin
     *
     * @return boolean true if all went well
     **/
    public function __construct(Cron $cron)
    {
        $this->cron = $cron;

        return true;
    }

    /**
     * undocumented function
     *
     * @return boolean true if all the expired sessions were cleaned
     **/
    public function execute($params = array())
    {
        return true;
    }
} // END class CleanSessions
