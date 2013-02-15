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

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;

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
            $url = self::normalizeUrl($this->request->getPathInfo());

            $parameters = $this->matcher->match($url);
            foreach ($parameters as $param => $value) {
                $this->request->query->set($param, $value);
            }

            $this->container->set('request', $this->request);

            $response = $this->dispatchRaw($parameters);

            if (is_string($response)) {
                $response = new Response($response);
            }

        } catch (ResourceNotFoundException $e) {
            $response = $this->handleException($e);
        } catch (\Exception $e) {
            $response = $this->handleException($e);
        }

        $response->send();
    }

    /**
     * Handles exceptions and redirect them to a controller
     *
     * @param  Exception $exception the exception that was raised
     * @return void
     **/
    public function handleException($exception)
    {
        $this->request->request->set('error', serialize($exception));
        $this->container->set('request', $this->request);
        return $this->dispatchClass($this->container->getParameter('dispatcher.exceptionhandler'));
    }

    /**
     * Handles the underlying controller
     *
     * @param  array $routeParameters the route parameter to dispatch
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
     * @param  string $controllerFileName the controller file path
     * @return string the response
     **/
    public function dispatchControllerFile($controllerFileName)
    {
        try {
            $response = include $controllerFileName;

            return $response;
        } catch (Exception $e) {
            throw new ResourceNotFoundException(
                "File '$controllerFileName' matched by the route don't exists."
            );
        }
    }

    /**
     * Dispatches a controller given its class name
     *
     * @param  string $className the class name to dispatch
     * @return string the response
     **/
    public function dispatchClass($className)
    {
        if (strpos($className, ':')) {
            list($controllerClassName, $actionName) =
                self::resolveClasNameAndAction($className);
            if (class_exists($controllerClassName)) {
                $module = self::resolveModuleName($controllerClassName);
                $this->initializeModule($module);
                $controller = new $controllerClassName();
                $controller->setContainer($this->container);
                $controller->init();

                $reflectionMethod = new \ReflectionMethod($controllerClassName, $actionName);
                $params = $reflectionMethod->getParameters();

                if (count($params) == 1) {
                    return $reflectionMethod->invokeArgs($controller, array($this->container->get('request')));
                } else {
                    return $controller->{$actionName}();
                }
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
     * @param string $controllerClassRaw the controller class and action
     * @return array array with the controller class name and action name
     **/
    public static function resolveClasNameAndAction($controllerClassRaw = '')
    {
        $parts = explode(':', $controllerClassRaw);
        $actionName = array_pop($parts)."Action";

        $controllerClassName = "\\".implode('\\', $parts);

        return array($controllerClassName, $actionName);
    }

    /**
     * Returns the module name given a full controller class name
     *
     * @param  string $controllerClassNAme the full controller class name
     * @return string the module name
     * @return Dispatcher the dispatcher instance
     **/
    public static function resolveModuleName($controllerClassName)
    {
        $cleanControllerClassName = ltrim($controllerClassName, '\\');
        $controllerClassNameParts = explode('\\', $cleanControllerClassName);
        $moduleName = $controllerClassNameParts[0];

        return $moduleName;
    }

    /**
     * Initiales the module that contains the controller to dispatch
     *
     * @param string $moduleName the module name
     * @return Dispatcher the dispatcher instance
     **/
    public function initializeModule($moduleName)
    {
        $moduleClassName = $moduleName.'\\Bootstrap';

        if (class_exists($moduleClassName)) {
            $moduleInstance = new $moduleClassName($this->container);
            $moduleInstance->init();
        }
        return $this;
    }

    /**
     * Cleans double slashes and trailing slash from an string url
     *
     * @param string $url the url to normalize
     * @return string the normalized url
     **/
    public static function normalizeUrl($url)
    {
        if (strlen($url) > 1) {
            $normalizedUrl = rtrim($url, '/');
        } else {
            $normalizedUrl = $url;
        }

        while (strpos($normalizedUrl, '//') != false) {
            $normalizedUrl = str_replace('//', '/', $normalizedUrl);
        }

        return $normalizedUrl;
    }
}
