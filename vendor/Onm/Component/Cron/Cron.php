<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Component\Cron;
/**
 * Handles all the cron actions, stores its parameters and provides statefull executions
 *
 * @package default
 * @author
 **/
class Cron
{
    /**
     * Stores all the registered plugins
     *
     * @var array
     **/
    private $_registeredPlugins;
    /**
     * Initializes the cron handler
     *
     * @author
     **/
    public function __construct()
    {
        return $this;
    }

    /**
     * Registers a plugin into the plugin list for executing
     *
     * If the $plugin parameter is an string the class tries to initialize and
     * register the class.
     * If the $plugin parameter is a plugin object adds it to the registered plugins.
     *
     * @return boolean true if the plugin was registered properly
     **/
    public function registerPlugin($plugin)
    {
        // if the parameter plugin is an object an implements the plugin inteface
        // register it
        if (
            is_object($plugin)
            && in_array('\Onm\Component\Cron\Plugin\PluginInterface',  class_implements($plugin))
        ) {
            $this->_registeredPlugins []= $plugin;
            return true;
        } elseif (is_string($plugin)) {
            // if the parameter plugin is an string and its the class name of an
            // available plugin register it.
            return true;
        }
        return false;
    }

    /**
     * Register an array of plugins
     *
     * @return boolean true if all went well
     **/
    public function registerPlugins($plugins)
    {
        $loaded = false;
        if (is_array($plugins)) {
            foreach ($plugins as $plugin) {
                $loaded &= $this->registerPlugin($plugin);
            }
        }
        return $loaded;
    }

} // END class Cron