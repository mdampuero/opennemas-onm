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
 * Onm_Service_OohEmbed
 * 
 * @package    Onm
 * @subpackage Service
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: OohEmbed.php 1 2010-07-06 12:22:52Z vifito $
 */
class Onm_Service_OohEmbed
{
    /**
     * Base URI for the REST client
     */
    const URI_BASE = 'http://oohembed.com/oohembed/?url=';    

    /**
     * Reference to Http client object
     *
     * @var Zend_Http_Client
     */
    protected $_httpClient = null;
    
    protected $_validSources = array(
        // Tested
        'http://\S*\.?youtube\.com/watch',
        'http://\S*\.?vimeo\.com/\S+',        
        'http://\S*\.?5min.com/Video/\S+',
        'http://\S*\.?amazon\.(com|co\.uk|de|ca|jp)/\S*/(gp/product|o/ASIN|obidos/ASIN|dp)/\S+',
        'http://\S*\.?blip\.tv/\S+',
        'http://\S*\.?wikipedia.org/wiki/\S+',
        'http://\S*\.?scribd\.com/\S+',
        'http://\S*\.?slideshare\.net/\S+', 
        
        // Untested
        'http://\S*\.?collegehumor\.com/video:\S+', 
        'http://\S*\.?thedailyshow\.com/video/\S+', 
        'http://\S*\.?dailymotion\.com/\S+', 
        'http://dotsub\.com/view/\S+', 
        'http://\S*\.?flickr\.com/photos/\S+', 
        'http://\S*\.?funnyordie\.com/videos/\S+', 
        'http://video\.google\.com/videoplay?\S+', 
        'http://www\.hulu\.com/watch/\S*', 
        'http://\S+\.livejournal\.com/', 
        'http://\S*\.?metacafe\.com/watch/\S+', 
        'http://\S*\.?nfb\.ca/film/\S+', 
        'http://\S*.?phodroid\.com/\S+/\S+/\S+', 
        'http://qik.com/\S+', 
        'http://\S*\.?revision3\.com/\S+',
        'http://\S*\.?twitpic\.com/\S+', 
        'http://twitter\.com/\S+/statuses/\S+', 
        'http://\S*\.?viddler\.com/explore/\S+',        
        'http://\S*\.?wordpress.com/[0-9]{4}/[0-1][0-9]/[0-9]{2}/\S+', 
        'http://\S*\.?xkcd.com/\S+/', 
        'http://yfrog\.(com|ru|com\.tr|it|fr|co\.il|co\.uk|com\.pl|pl|eu|us)/\S+',        
    );


    /**
     * Performs object initializations
     *
     *  # Sets up character encoding
     *  # Saves the API key
     *
     * @param  string $apiKey Your Flickr API key
     * @return void
     */
    public function __construct()
    {
        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');
    }
    
    
    /**
     *
     * @param string $url
     * @return array 
     */
    public function slurp($url)
    {
        
        // TODO: check if $url is valid
        $url = self::URI_BASE . urlencode($url);
        
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
        
        $result = json_decode($response->getBody());
        
        return $result;
    }
    
    
    /**
     * Check if $uri is a valid resource
     * 
     * @param string $uri
     * @return boolean
     */
    public function isValidURI($uri)
    {
        foreach ($this->_validSources as $source) {
            $regex = '@' . $source . '@';
            if (preg_match($regex, $uri)) {
                return true;
            }
        }
        
        return false;
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
    
    
} // END: class Onm_Service_OohEmbed