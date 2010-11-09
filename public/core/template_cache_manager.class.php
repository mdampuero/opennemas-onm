<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
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
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * TemplateCacheManager class manage the smarty cache.
 *
 * @package    OpenNemas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://www.openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TemplateCacheManager
{
    public $cacheGroups = array();
    protected $cacheDir = null;
    protected $smarty   = null;

    /**
     * Construct
     * @param string $themeDir Path to smarty theme
     * @param Smarty $smarty Smarty class
     */
    public function __construct($themeDir, $smarty=null)
    {
        $this->smarty   = (!is_null($smarty))? $smarty: new Template($themeDir);
        $this->cacheDir = $themeDir . 'cache/';
    }

    /**
     * Scan cache directory and return an array with cache files
     *
     * @param string $filter A regular expression to filter cache file names
     * @return array Array of cache files
     */
    public function scan($filter=null)
    {
        $caches = array();
        $matches =array();

        $dirIt = new DirectoryIterator($this->cacheDir);
        foreach($dirIt as $item) {
            if($item->isDot()) {
                continue;
            }

            $filename = $item->current()->getFilename();

            if( preg_match('/^(?P<category>[^\^]+)\^([^\^]+)\^/', $filename, $matches)) {
                $this->cacheGroups[] = $matches['category'];

             }

            if(empty($filter) || preg_match($filter, $filename)) {
                preg_match('/^(?P<category>[^\^]+)\^(?P<resource>[^\^]+)\^(.*?)(?P<tplname>[^%^.]+)\.tpl\.php$/',
                       $filename, $matches);

                if(isset($matches['category'])) {
                    $caches[] = array('category' => $matches['category'],
                                      'resource' => $matches['resource'],
                                      'template' => $matches['tplname'],
                                      'size'     => $item->current()->getSize(),
                                      'filename' => $filename);
                }
            }
        }

        return $caches;
    }

    /**
     * Load content of smarty cache into list
     *
     * @param array $caches
     */
    public function parseList(array &$caches) {
        for($i=0, $total=count($caches); $i<$total; $i++) {
            $data = $this->parse($caches[$i]['filename']);
            $caches[$i]['expires'] = $data['expires'];
            $caches[$i]['created'] = $data['timestamp'];
        }
    }

    /**
     * Parse a cache file and extract smarty information
     *
     * @param string $cacheFileName Name of cache
     * @return array Array smarty information
     */
    public function parse($cacheFileName)
    {
        // Clear cache of filesystem to get updated information
        /* clearstatcache(); */

        $data = null;
        if( file_exists($this->cacheDir.$cacheFileName) ) {
            $createTime = filectime($this->cacheDir.$cacheFileName);
            $fp = fopen($this->cacheDir.$cacheFileName, 'r');

            if ($fp) {
                if (!feof($fp)) {
                    // Read first line
                    $strLen = fgets($fp, 4096);
                    // Convert to int the length information
                    $strlen = intval($strLen);
                }

                if (!feof($fp)) {
                    // Read second line with smarty information
                    $data = fgets($fp, $strLen+1);
                }
                fclose($fp);
            }

            if(!is_null($data)) {
                $data = unserialize($data);
            }

            $data['timestamp'] = $createTime;
            $data['expires'] = $createTime;
        }

        return $data;
    }

    /**
     * Get a exact name for a cache ID
     *
     * @see function scan
     * @param $cacheId Cache ID
     * @param $tplFilename Template file name, extension must be included (sample: index.tpl)
     * @return string Return a cache file name
     */
    public function getCacheFileName($cacheId, $tplFilename=null)
    {
        // Smarty convert the "|" character for a "^" character
        $cacheId = str_replace('|', '^', $cacheId);

        // Make a regular expression to filter
        $filter = '/^' . preg_quote($cacheId) . '\^.*?';
        if(!is_null($tplFilename)) {
            $filter .= preg_quote($tplFilename).'\.php$';
        }
        $filter .= '/';

        // Scan directory applying the filter
        $caches = $this->scan($filter);

        if(count($caches) > 1) {
            $names = array();
            foreach($caches as $cache) {
                $names[] = $cache['filename'];
            }

            // Return an array of names of cache
            return $names;
        } elseif(count($caches) == 0) {

            // Fail searching filename
            return null;
        }

        // Return cache filename
        return $caches[0]['filename'];
    }

    /**
     * Delete a cache file physically
     *
     * @param string $cachefile Cache file or cache Id
     * @param string $tplFilename Template file name
     * @return boolean Return a boolean information of operation performed
     */
    public function delete($cachefile, $tplFilename=null) {
        $cachefile = $this->getCacheFileName($cachefile, $tplFilename);

        if(is_array($cachefile) && count($cachefile) > 1) {
            foreach($cachefile as $name) {
                $filename = $this->cacheDir.$name;
                $this->removeFile($filename);
            }
        } elseif(!empty($cachefile)) {
            $cachefile = $this->cacheDir.$cachefile;

            $this->removeFile($cachefile);
            return true;
        }

        return false;
    }

    /**
     * Clear all caches of a group
     *
     * @param string $cacheGroup Name of group
     */
    public function clearGroupCache($cacheGroup) {
        $this->smarty->clear_cache(null, $cacheGroup);
    }

    /**
     * Refresh timestamp of expires for a cachefile or cacheId
     *
     * @param int $timestamp New timestamp to expires
     * @param string $cachefile Name of cache file or cache Id
     * @param string $tplFilename Optional name of template
     * @return boolean Return true if action is performed
     */
    public function update($timestamp, $cachefile, $tplFilename=null)
    {
        // To understand this section it's necessary knowledge of smarty internals
        if(!is_null($tplFilename)) {
            // $cachefile is $cacheId if $tplFilenama isn't null
            $cachefile = $this->getCacheFileName($cachefile, $tplFilename);

            if(is_array($cachefile)) {
                throw new Exception('TemplateCacheManager::Update operation only support an one cache file.');
            }
        }
        $cachefile = $this->cacheDir.$cachefile;

        $data = null;
        if( file_exists($cachefile) ) {
            // Dump content  to lines array
            $lines = file($cachefile);

            // Length of serialized data
            $strlen = intval($lines[0]);

            // Get and unserialize cache information
            $data = substr($lines[1], 0, $strlen);
            $data = unserialize( $data );

            // Modify expires information
            $data['expires'] = $timestamp;

            // Serialize
            $data = serialize($data);
            $line = substr($lines[1], $strlen);

            $lines[1] = $data.$line;

            file_put_contents($cachefile, implode('', $lines));

            return true;
        }

        return false;
    }

    /**
     * Fetch uri to update cache
     *
     * @param string $uri URI to fetch
     */
    public function fetch($uri) {
        // cURL Handle
        $ch = curl_init();

        // Options
        curl_setopt($ch, CURLOPT_URL,            $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // Capture output
        curl_setopt($ch, CURLOPT_HEADER,         false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Don't verify certificate
        curl_setopt($ch, CURLOPT_USERAGENT,      'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9) Gecko/2008052906 Firefox/3.0');

        ob_start();
        // Exec
        $html = curl_exec($ch);
        ob_end_clean();

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return $http_code==200;
    }

    public function getResources($items=array()) {
        $pk_contents = array();
        $pk_authors = array();

        foreach($items as $item) {
            if(preg_match('/[0-9]{14,19}/', $item['resource'])) {
                $pk_contents[] = $item['resource'];
            } elseif( preg_match('/RSS([0-9]+)/', $item['resource'], $matches) ) {
                $pk_authors[] = $matches[1];
            }
        }

        return array( $pk_contents, $pk_authors );
    }

    protected function removeFile($filename)
    {
        if(file_exists($filename)) {
            unlink($filename);
            return true;
        }
    }

    public function dumpConfig()
    {
        $filename = $this->smarty->config_dir . 'cache.conf';
        return parse_ini_file($filename, true);
    }

    public function saveConfig($config)
    {
        $filename = $this->smarty->config_dir . 'cache.conf';
        $fp = fopen($filename, 'w');

        if($fp !== false) {
            foreach($config as $section => $entry) {
                fputs($fp, '[' . $section . ']' . "\n");
                fputs($fp, 'caching = ' . $entry['caching'] . "\n");
                fputs($fp, 'cache_lifetime = ' . $entry['cache_lifetime'] . "\n\n");
            }

            fclose($fp);
        } else {
            throw new Exception('Error open file: ' . $filename);
        }
    }
}
