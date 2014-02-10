<?php
/**
 * Defines the Onm\Rest\Manager\AuthSystem class
 *
 * This file is part of the onm package.
 * (c) 2009-2013 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_Rest
 **/
namespace Onm\Rest\Manager;

/**
 * Handles the authentication protocol for the Lucarast\Restler into Onm
 *
 * @package    Onm_Rest
 **/
class AuthSystem implements \Luracast\Restler\iAuthenticate
{
    /**
     * Key used for authenticate users
     *
     * @var
     **/
    private $key;

    /**
     * Sets the key for the auth layer
     *
     * @return void
     **/
    private function setKey()
    {
        //Get key from congig
        if (isset($this->restler->wsParams["api_key"])) {
            $this->key = $this->restler->wsParams["api_key"];
        }
    }

    /**
     * Magick method for checking if the user has access to the ws
     *
     * @return boolean true if the user has no access
     **/
    public function __isAllowed()
    {
        if (!$this->isHttps()) {
            return false;
        }
        $this->setKey();
        $function = $this->restler->url;
        if (isset($this->restler->requestData['timestamp']) &&
            ($timestamp = $this->restler->requestData['timestamp'])) {
            $signature = hash_hmac(
                'sha1',
                $timestamp.$function.$timestamp,
                $this->key
            );

            if (isset($this->restler->requestData['signature']) &&
                ($signature == $this->restler->requestData['signature'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if the request was done through encrypted HTTP
     *
     * @return boolean true if the request was done with HTTPS
     **/
    private function isHttps()
    {
        if (!empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS']!="off"
        ) {
            return true;
        }
        return false;
    }
}
