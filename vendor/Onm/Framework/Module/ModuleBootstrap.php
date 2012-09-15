<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Framework\Module;

/**
 * Handles the common operations in module bootstrap and initialization
 *
 * @package Onm_Framework_Module_ModuleBootstrap
 **/
class ModuleBootstrap
{
    /**
     * Initialies the module given the general container
     *
     * @return void
     **/
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function init()
    {
        $reflection = new \ReflectionClass($this);
        $methods = $reflection->getMethods();
        foreach ($methods as $method) {
            $methodName = $method->getName();
            $match = preg_match("@init(.)+@", $method->getName(), $matches);
            if ($match) {
                $this->{$methodName}();
            }
        }
        return $this;
    }
}

