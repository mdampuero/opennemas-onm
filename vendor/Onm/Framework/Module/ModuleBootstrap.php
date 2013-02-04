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
        $this->reflection = new \ReflectionClass($this);
        $this->container = $container;

        $declaringClassFileName = $this->reflection->getFileName();
        $this->moduleBasePath = dirname($declaringClassFileName);
    }

    /**
     * Initializes all the event listener classes if available for a given module
     *
     * @return void
     **/
    public function initEventListeners()
    {
        try {
            $dispatcher = $this->container->get('event_dispatcher');
        } catch (\Exception $e) {
            return false;
        }

        $eventListenersPath = $this->moduleBasePath.'/EventListeners';
        if (is_dir($eventListenersPath)) {
            $namespace = $this->reflection->getNamespaceName();

            $listenerClasses = glob($eventListenersPath.'/*.php');
            foreach ($listenerClasses as $className) {
                $eventListenerClassName = $namespace.'\\EventListeners\\'.basename($className, '.php');
                $listenerInstance = new $eventListenerClassName;

                $dispatcher->addSubscriber($listenerInstance);
            }
        }
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function init()
    {
        $methods = $this->reflection->getMethods();
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
