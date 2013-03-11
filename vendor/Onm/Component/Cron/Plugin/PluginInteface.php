<?php
/**
 * Defines the Onm\Component\Cron\PluginInterface interface
 *
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Onm_Compoment_Cron
 **/
namespace Onm\Component\Cron\Plugin;

/**
 * Interface for Cron plugins
 *
 * @package Onm_Compoment_Cron
 **/
interface PluginInterface
{
    /**
     * Executes the plugin logic given an array of parameters
     *
     * @param array $params the params to execute the cron action
     *
     * @return boolean true if the plugin was executed properly
     **/
    public function execute($params = array());
}
