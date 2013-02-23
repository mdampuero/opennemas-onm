<?php

/**
 * This file is part of the onm package.
 * (c) 2009-2013 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
/**
 *
 *
 * @package    Onm
 * @subpackage Rest
 * @author     me
 **/
namespace Onm\Rest\Manager;

class AuthSystem implements \Luracast\Restler\iAuthenticate
{

    private $key;

    private function setKey()
    {
        //Get key from congig
        if (isset($this->restler->wsParams["api_key"])) {
            $this->key = $this->restler->wsParams["api_key"];
        }
    }

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
