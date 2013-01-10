<?php

class AuthSystem implements iAuthenticate{

    const KEY = 'sheequaiJie5deewijie6oojaiShie5R';

    function __isAuthenticated() {
    	if (!$this->isHttps()) return FALSE;
        $function = $this->restler->url;
        isset($this->restler->request_data['timestamp'])?
            ($timestamp = $this->restler->request_data['timestamp']):FALSE;
        $signature = hash_hmac(
            'sha1',
            $timestamp.$function.$timestamp,
            AuthSystem::KEY
        );
    	if(isset($this->restler->request_data['signature']) &&
    		($signature == $this->restler->request_data['signature']))
    	   return TRUE;
        return FALSE;
    }

    private function isHttps() {
    	if( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!="off" )
    		return TRUE;
    	else return FALSE;
    }

}