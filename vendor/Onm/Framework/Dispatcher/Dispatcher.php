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

use \Symfony\Component\Routing\Exception\ResourceNotFoundException,
    \Symfony\Component\HttpFoundation\Response;
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
    public function __construct($matcher, &$request, $container)
    {
        $this->matcher = $matcher;
        $this->request = $request;
        $this->container = $container;
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

            $this->container->set('request',$this->request);

            $response = $this->dispatchRaw($parameters);

            if (is_string($response)) {
                $response = new Response($response);
            }

            $response->send();

        } catch (ResourceNotFoundException $e) {
            $this->handleException($e);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Handles exceptions and redirect them to a controller
     *
     * @return void
     * @author
     **/
    public function handleException($exception)
    {
        $this->request->request->set('error', serialize($exception));
        $this->container->set('request',$this->request);
        $this->dispatchClass('Framework:Controllers:ErrorController:default');
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
            $response = include $controllerFileName;
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
                $controller = new $controllerClassName();
                $controller->setContainer($this->container);
                $controller->init();
                return $controller->{$actionName}();
            } else {
                throw new ResourceNotFoundException(
                    "Route class '$controllerClassName' don't exists."
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