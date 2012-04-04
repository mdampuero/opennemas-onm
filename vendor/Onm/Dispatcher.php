<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm;
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

            // If the matched url has the plain controller format require the straight file
            if (array_key_exists('_controllerfile', $parameters)) {
                require $parameters['_controllerfile'];
            } else {
                $controller = new $parameters['_controller']($this->request);
            }
        } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
            echo '[not implemented] route not found';
        }
    }

    /**
     * Cleans double slashes and trailing slash from an string url
     *
     * @return void
     * @author
     **/
    public function normalizeUrl($url)
    {
        if (strlen($url) > 1) { $url = rtrim($url,'/'); }
        $url = str_replace('//', '/', $url);
        return $url;
    }
} // END class Dispatcher