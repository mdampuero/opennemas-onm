<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Framework\Dispatcher;

use \Symfony\Component\Routing\Exception\ResourceNotFoundException;
/**
 * Dispatches url to a matched controller
 *
 * @package Onm
 * @author
 **/
class Dispatcher
{
    /**
     * Initializes the dispatcher
     *
     * @return this
     **/
    public function __construct($matcher, &$request)
    {
        $this->matcher = $matcher;
        $this->request = $request;
    }

    /**
     * Dispatches an url to its controller
     *
     * @return boolean
     **/
    public function dispatch()
    {
        try {
            $url = $this->normalizeUrl($this->request->getPathInfo());

            $parameters = $this->matcher->match($url);
            foreach ($parameters as $param => $value) {
                $this->request->query->set($param, $value);
            }

            $this->dispatchRaw($parameters);

        } catch (ResourceNotFoundException $e) {
            $this->request->request->set('error', serialize($e));
            $this->dispatchClass('Framework:Controllers:ErrorController:default');
        }
    }

    /**
     * Handles the underlying controller
     *
     * @return string the response string
     **/
    public function dispatchRaw($routeParameters)
    {
        // If the matched url has the plain controller format require the straight file
        if (array_key_exists('_controllerfile', $routeParameters)) {
            return $this->dispatchControllerFile($routeParameters['_controllerfile']);
        } else {
            // Experimental class-based controllers code
            return $this->dispatchClass($routeParameters['_controller']);
        }
    }

    /**
     * Dispatches a controller given its filename
     *
     * @return string the response
     **/
    public function dispatchControllerFile($controllerFileName)
    {
        try {
            $response = require $controllerFileName;
            return $response;
        } catch (Exception $e) {
            throw new ResourceNotFoundException(
                "Route '$className' don't exists."
            );
        }
    }

    /**
     * Dispatches a controller given its class name
     *
     * @return string the response
     **/
    public function dispatchClass($className)
    {
        if (strpos($className, ':')) {
            list($controllerClassName, $actionName) =
                self::resolveClasNameAndAction($className);
            if (class_exists($controllerClassName)) {
                $controller = new $controllerClassName($this->request);
                $controller->init();
                return $controller->{$actionName}();
            } else {
                throw new ResourceNotFoundException(
                    "Route class '$className' don't exists."
                );
            }

        }
    }

    /**
     * Transforms the Raw ClassName to the proper
     *
     * @return void
     **/
    public static function resolveClasNameAndAction($controllerClassRaw = '')
    {
        $parts = explode(':', $controllerClassRaw);
        $actionName = array_pop($parts)."Action";

        $controllerClassName = "\\".implode('\\', $parts);

        return array($controllerClassName, $actionName);
    }

    /**
     * Cleans double slashes and trailing slash from an string url
     *
     * @return void
     * @author
     **/
    public function normalizeUrl($url)
    {
        if (strlen($url) > 1) {
            $normalizedUrl = rtrim($url,'/');
        } else {
            $normalizedUrl = $url;
        }
        while (strpos($normalizedUrl, '//') != false) {
            $normalizedUrl = str_replace('//', '/', $url);
            echo $normalizedUrl;
        }

        return $normalizedUrl;
    }
} // END class Dispatcher