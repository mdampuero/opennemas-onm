<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all the operations with proxies.
 *
 * @package    Onm
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class Proxy {
    /**
     * @var string URI
     */
    private $url = null;

    private $content = null;

    private $_contentType = null;

    /**
     * @var MethodCacheManager Handler to call method cached
     */
    public $cache = null;

    /**
     * constructor
     *
     * @param int $id
     */
    public function __construct($url=null, $_contentType=null)
    {
        if (!is_null($url)) {
            $this->setUrl($url);
        }

        if (!is_null($_contentType)) {
            $this->set_contentType($_contentType);
        }

        $this->cache = new MethodCacheManager($this, array('ttl' => 60));
    }

    /**
     *
     *
     */
    public function setUrl($url)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        if ($url !== false) {
            $this->url = $url;
        } else {
            throw new Exception(_('URL not valid'));
        }

        return $this; // Chaining methods
    }

    public function set_contentType($content_type)
    {
        $this->_contentType = $content_type;

        return $this; // Chaining methods
    }

    /**
     *
     */
    public function get()
    {
        if (!is_null($this->url)) {
            list($this->content, $this->_contentType) = $this->exec($this->url);
        }

        return $this; // Chaining methods
    }

    public function dump()
    {
        if (!is_null($this->_contentType) && !is_null($this->content)) {
            header('Content-type: ' . $this->_contentType);
            echo $this->content;
        } else {
            header("HTTP/1.0 404 Not Found");
        }
    }

    /**
     * Perform HTTP request
     *
     * @param string $url
     * @return string HTML/XML content response
     */
    private function exec($url)
    {
        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

            $result = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($code == 200) {
                return array(
                    $result, curl_getinfo($curl, CURLINFO_CONTENT_TYPE)
                );
            } else {
                return array(null, null);
            }
        }
    }

}
