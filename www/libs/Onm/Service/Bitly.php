<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNemas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNemas
 * @package    OpenNemas
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Onm_Service_Bitly
 *
 * <code>
 * $bitly = new Onm_Service_Bitly($login, $key);
 *
 * $result = $bitly->validate($login, $key);
 * 
 * $result = $bitly->clicks( array('http://bit.ly/644UZ', '6uBaUZ') );
 * $result = $bitly->clicks( 'http://bit.ly/644UZ' );
 *
 * $result = $bitly->expand( array('http://bit.ly/644UZ', '6uBaUZ') );
 * $result = $bitly->expand( '6uBaUZ' );
 *
 * $result = $bitly->shorten( 'http://vifito.eu' );
 * </code>
 * 
 * @package    Onm
 * @subpackage Service
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Onm_Service_Bitly.php 1 2010-05-29 12:13:00Z vifito $
 */
class Onm_Service_Bitly
{
    /**
     * Base URI for the REST client
     */
    const URI_BASE = 'http://api.bit.ly';

    /**
     * Your bit.ly API key
     *
     * @var string
     */
    public $apiKey;
    
    /**
     * Your login account
     *
     * @var string
     */
    public $login;
    
    /**
     * Format response
     *
     * @var string
     */
    public $format = 'xml'; 

    /**
     * Reference to Http client object
     *
     * @var Zend_Http_Client
     */
    protected $_httpClient = null;


    /**
     * Performs object initializations
     *
     *  # Sets up character encoding
     *  # Saves the API key
     *
     * @param  string $apiKey Your Flickr API key
     * @return void
     */
    public function __construct($login, $apiKey)
    {
        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');
        
        $this->login  = (string) $login;
        $this->apiKey = (string) $apiKey;
    }
    
    
    /**
     * 
     *
     * @return boolean  
     */
    public function validate($x_login, $x_apiKey)
    {                
        $options['x_login']  = $x_login;
        $options['x_apiKey'] = $x_apiKey;
        
        $options = $this->_setDefaultOptions($options);
        $url = self::URI_BASE . '/v3/validate?' . $this->_buildQueryString($options);
        
        $client = $this->getHttpClient();
        $client->setUri($url);
        
        $response = $client->request();
        
        if ($response->isError()) {
            /**
             * @see Zend_Service_Exception
             */
            require_once 'Zend/Service/Exception.php';
            throw new Zend_Service_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        
        $xml = new SimpleXMLElement($response->getBody());
        
        return $xml->data->valid == 1;
    }
    
    
    /**
     *
     * <code>
     * Array (
     *   [0] => Array
     *   (
     *       [short_url] => http://bit.ly/644UZ
     *       [error] => NOT_FOUND
     *   )
     *   [1] => Array
     *   (
     *       [user_clicks] => 8
     *       [global_hash] => 7P9yEn
     *       [hash] => 6uBaUZ
     *       [user_hash] => 6uBaUZ
     *       [global_clicks] => 8
     *   )
     *   ...
     * </code>
     *
    */
    public function clicks($shortUrl)
    {
        $options = $this->_setShortUrls($shortUrl);
        
        $options = $this->_setDefaultOptions($options);
        $url = self::URI_BASE . '/v3/clicks?' . $this->_buildQueryString($options);
        
        $client = $this->getHttpClient();
        $client->setUri($url);
        
        $response = $client->request();                
        
        if ($response->isError()) {
            /**
             * @see Zend_Service_Exception
             */
            require_once 'Zend/Service/Exception.php';
            throw new Zend_Service_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        
        $xml = new SimpleXMLElement($response->getBody());
        
        $result = array();        
        
        for($i=0; $i < count($xml->data->clicks); $i++) {
            foreach($xml->data->clicks[$i] as $k => $d) {
                $result[$i][$k] = (string)$d;
            }
        }
        
        return $result;
    }
    
    
    /**
     *
     *
     * <code>
     * Array (
     *   [0] => Array
     *       (
     *           [error] => NOT_FOUND
     *           [short_url] => http://bit.ly/644UZ
     *       )
     *   
     *   [1] => Array
     *       (
     *           [hash] => 6uBaUZ
     *           [long_url] => http://vifito.eu/codigo-fonte/15-javafx/28-alternativa-...
     *           [user_hash] => 6uBaUZ
     *           [global_hash] => 7P9yEn
     *       )
     *   ...
     * </code>
     */
    public function expand($shortUrl)
    {
        $options = $this->_setShortUrls($shortUrl);
        
        $options = $this->_setDefaultOptions($options);
        $url = self::URI_BASE . '/v3/expand?' . $this->_buildQueryString($options);
        
        $client = $this->getHttpClient();
        $client->setUri($url);
        
        $response = $client->request();                
        
        if ($response->isError()) {
            /**
             * @see Zend_Service_Exception
             */
            require_once 'Zend/Service/Exception.php';
            throw new Zend_Service_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        
        $xml = new SimpleXMLElement($response->getBody());
        
        $result = array();        
        
        for($i=0; $i < count($xml->data->entry); $i++) {
            foreach($xml->data->entry[$i] as $k => $d) {
                $result[$i][$k] = (string)$d;
            }
        }
        
        return $result;
    }
    
    
    /**
     * 
     *
     * <code>
     * Array (
     *   [url] => http://bit.ly/94serl
     *   [hash] => 94serl
     *   [global_hash] => aVwMiG
     *   [long_url] => http://vifito.eu
     *   [new_hash] => 0
     * )
     * </code>
     *
     * 
     */
    public function shorten($longUrl, $x_login=null, $x_apiKey=null, $domain='bit.ly')
    {
        $options['longUrl'] = urlencode($longUrl);
        $options['domain'] = urlencode($domain);
        
        if(!is_null($x_login) && !is_null($x_apiKey)) {
            $options['x_login']  = $x_login;
            $options['x_apiKey'] = $x_apiKey;
        }
        
        $options = $this->_setDefaultOptions($options);
        $url = self::URI_BASE . '/v3/shorten?' . $this->_buildQueryString($options);
        
        $client = $this->getHttpClient();
        $client->setUri($url);
        
        $response = $client->request();                
        
        if ($response->isError()) {
            /**
             * @see Zend_Service_Exception
             */
            require_once 'Zend/Service/Exception.php';
            throw new Zend_Service_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        
        $xml = new SimpleXMLElement($response->getBody());        
        
        $result = array();        
        foreach($xml->data->children() as $k => $d) {
            $result[$k] = (string)$d;
        }
        
        return $result;
    }
    
    
    private function _buildQueryString($options)
    {
        $qstring = array();
        
        foreach($options as $k => $v) {
            if(!is_array($v)) {
                $qstring[] = $k . '=' . $v;
            } else {
                foreach($v as $_v) {
                    $qstring[] = $k . '=' . $_v;
                }
            }
        }
        
        return implode('&', $qstring);
    }
    
    
    /**
     * Returns a reference to the HTTP client, instantiating it if necessary
     *
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        if (null === $this->_httpClient) {
            /**
             * @see Zend_Rest_Client
             */
            require_once 'Zend/Http/Client.php';
            $this->_httpClient = new Zend_Http_Client();
        }
        
        return $this->_httpClient;
    }
    
    private function _setDefaultOptions($options, $defaultOptions=array())
    {
        $defaultOptions['login']  = $this->login;
        $defaultOptions['apiKey'] = $this->apiKey;
        $defaultOptions['format'] = $this->format;
        
        return $options + $defaultOptions;
    }
    
    private function _setShortUrls($shortUrl)
    {
        $options = array();
        
        if(!is_array($shortUrl)) {
            if(preg_match('|^http://|', $shortUrl)) {
                $options['shortUrl'] = urlencode($shortUrl);
            } else {
                // is an hash
                $options['hash'] = $shortUrl;
            }
        } else {
            $options = array(
                'shortUrl' => array(),
                'hash'     => array(),
            );
            
            foreach($shortUrl as $url) {
                if(preg_match('|^http://|', $url)) {
                    $options['shortUrl'][] = urlencode($url);
                } else {
                    // is an hash
                    $options['hash'][] = $url;
                }
            }
        }
        
        return $options;
    }
    
}


