<?php
/**
 * Defines the OnmAuth class
 *
 * This file is part of the onm package.
 * (c) 2009-2013 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 **/
use Onm\Settings as s;

/**
 * Handles the authentication protocol for the Onm News Agency
 *
 **/
class OnmAuth implements iAuthenticate
{
    public function __isAuthenticated()
    {
        return isset($_GET['auth']) && $_GET['auth']==$this->key() ? true : false;
    }

    public function key()
    {
        return s::get('onm_auth_key');
    }
}
