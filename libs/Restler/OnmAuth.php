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

        $realm = 'Enter your credentials';

        // Just a random id
        $nonce = uniqid();

        // Get the digest from the http header
        $digest = $this->getDigest();

        // If there was no digest, show login
        if (is_null($digest)) {
            $this->requireLogin($realm, $nonce);
        }

        // Parse digest string
        $digestParts = $this->digestParse($digest);

        // Get valid user
        $validUser = s::get('onm_digest_user');
        $validPass = s::get('onm_digest_pass');

        // Based on all the info we gathered we can figure out what the response should be
        $A1 = md5($digestParts['username'].':'.$realm.':'.$validPass);
        $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$digestParts['uri']);
        $validResponse = md5(
            $A1.
            ':'.$digestParts['nonce'].
            ':'.$digestParts['nc'].
            ':'.$digestParts['cnonce'].
            ':'.$digestParts['qop'].
            ':'.$A2
        );

        $response = true;
        if ($digestParts['response'] != $validResponse) {
             $response = $this->requireLogin($realm, $nonce);
        }

        return $response;
    }

    // This function returns the digest string
    private function getDigest()
    {
        $digest = null;

        // mod_php
        if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
            $digest = $_SERVER['PHP_AUTH_DIGEST'];
            // most other servers
        } elseif (isset($_SERVER['HTTP_AUTHENTICATION'])) {

            if (strpos(strtolower($_SERVER['HTTP_AUTHENTICATION']), 'digest') === 0) {
                $digest = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
            }
        }

        return $digest;

    }

    // This function forces a login prompt
    private function requireLogin($realm, $nonce)
    {
        header(
            'WWW-Authenticate: Digest realm="' . $realm .
            '",qop="auth",nonce="' . $nonce . '",opaque="' . md5($realm) . '"'
        );
        header('HTTP/1.0 401 Unauthorized');

        return false;
    }

    // This function extracts the separate values from the digest string
    private function digestParse($digest)
    {
        // Protect against missing data
        $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
        $data = array();
        $keys = implode('|', array_keys($needed_parts));

        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $digest, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($needed_parts[$m[1]]);
        }

        return $needed_parts ? false : $data;
    }
}
