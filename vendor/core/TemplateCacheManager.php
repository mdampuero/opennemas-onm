<?php
/**
 * Defintes the TemplateCacheManager class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Core
 */

/**
 * TemplateCacheManager class manage the smarty cache.
 *
 * @package Core
 */
class TemplateCacheManager
{
    public $cacheGroups = array();
    public $properties = array();
    protected $cacheDir = null;
    protected $smarty = null;

    /**
     * Construct
     * @param string $themeDir Path to smarty theme
     * @param Smarty $smarty   Smarty class
     */
    public function __construct($themeDir, $smarty = null)
    {
        if (!is_null($smarty)) {
            $this->smarty = $smarty;
        } else {
            $this->smarty = new Template($themeDir);
        }
        $this->cacheDir = $this->smarty->cache_dir;

    }

    /**
     * Scan cache directory and return an array with cache files
     *
     * @param  string $filter A regular expression to filter cache file names
     * @return array  Array of cache files
     */
    public function scan($filter = null)
    {
        $caches  = array();
        $matches = array();
        $dirIt   = new DirectoryIterator($this->cacheDir);
        foreach ($dirIt as $item) {
            if ($item->isDot()) {
                continue;
            }
            $filename = $item->current()->getFilename();

            $regex = '/^(?P<category>[^\^]+)\^([^\^]+)\^/';
            if (preg_match($regex, $filename, $matches)) {
                $this->cacheGroups[] = $matches['category'];
            }

            if (empty($filter) || preg_match($filter, $filename)) {
                $regex = '/^(?P<category>[^\^]+)\^(?P<resource>[^\^]+)?\^(.*?)'
                    .'(?P<tplname>[^%^.]+)\.tpl\.php$/';
                preg_match($regex, $filename, $matches);

                if (isset($matches['category'])) {
                    $caches[] = array(
                        'category' => $matches['category'],
                        'resource' => $matches['resource'],
                        'template' => $matches['tplname'],
                        'size' => number_format(
                            $item->current()->getSize() / 1024,
                            2
                        ),
                        'filename' => $filename,
                    );
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
    public function parseList($caches)
    {

        for ($i = 0, $total = count($caches); $i < $total; $i++) {
            $data = $this->parse($caches[$i]['filename']);
            $caches[$i]['expires'] = $data['expires'];
            $caches[$i]['created'] = $data['timestamp'];
        }

        return $caches;
    }

    /**
     * Parse a cache file and extract smarty information
     *
     * @param string $cacheFileName Name of cache
     *
     * @return array Array smarty information
     */
    public function parse($cacheFileName)
    {
        // Clear cache of filesystem to get updated information
        /* clearstatcache(); */

        $data = null;
        if (file_exists($this->cacheDir . $cacheFileName)) {
            $data = $this->getHeaderInfoFromCacheFile(
                $this->cacheDir . $cacheFileName
            );
            $data['timestamp'] = filectime($this->cacheDir . $cacheFileName);
            $data['expires'] = $data['timestamp'] + $data['cache_lifetime'];
        }

        return $data;
    }

    /**
     * Decodes information of smarty cache files
     *
     * @param mixed $properties  array containing imported properties from cachefile
     *
     * @return void
     */
    public function decodeProperties($properties)
    {

        $this->has_nocache_code = $properties['has_nocache_code'];
        $this->properties['nocache_hash'] = $properties['nocache_hash'];
        if (isset($properties['cache_lifetime'])) {
            $this->properties['cache_lifetime'] = $properties['cache_lifetime'];
        }
        if (isset($properties['file_dependency'])) {
            $this->properties['file_dependency'] =
                $properties['file_dependency'];
        }
        if (!empty($properties['function'])) {
            $this->properties['function'] = array_merge($this->properties['function'], $properties['function']);
            $this->smarty->template_functions = array_merge($this->smarty->template_functions, $properties['function']);
        }
    }

    /**
     * Obtains the cache information from one Smarty cache file.
     *
     * @param string $filename, the path to the cache file where extract info
     *
     * @return mixed, an array containing information of smarty cache files
     */
    public function getHeaderInfoFromCacheFile($file)
    {
        $this->properties = array();

        $_smarty_tpl = $this;
        $no_render = true;

        $output = include($file);

        unset($no_render);

        return $this->properties;
    }

    /**
     * Get a exact name for a cache ID
     *
     * @see function scan
     * @param $cacheId Cache ID
     * @param $tplFilename Template file name, extension must be
     *                              included (sample: index.tpl)
     *
     * @return string Return a cache file name
     */
    public function getCacheFileName($cacheId, $tplFilename = null)
    {
        // Smarty convert the "|" character for a "^" character
        $cacheId = str_replace('|', '^', $cacheId);

        // Make a regular expression to filter
        $filter = '/^' . preg_quote($cacheId) . '\^.*?';

        if (!is_null($tplFilename)) {
            $filter.= preg_quote($tplFilename) . '\.php$';
        }
        $filter.= '/';

        // Scan directory applying the filter
        $caches = $this->scan($filter);

        if (count($caches) > 1) {
            $names = array();
            foreach ($caches as $cache) {
                $names[] = $cache['filename'];
            }

            // Return an array of names of cache
            return $names;
        } elseif (count($caches) == 0) {

            // Fail searching filename
            return null;
        }

        // Return cache filename
        return $caches[0]['filename'];
    }

    /**
     * Delete a cache file physically
     *
     * @param string $cachefile   Cache file or cache Id
     * @param string $tplFilename Template file name
     *
     * @return boolean Return a boolean information of operation performed
     */
    public function delete($cachefile, $tplFilename = null)
    {
        $cachefile = $this->getCacheFileName($cachefile, $tplFilename);

        if (is_array($cachefile) && count($cachefile) > 1) {
            foreach ($cachefile as $name) {
                $filename = $this->cacheDir . $name;
                $this->removeFile($filename);
            }
        } elseif (!empty($cachefile)) {
            $cachefile = $this->cacheDir . $cachefile;
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
    public function clearGroupCache($cacheGroup)
    {

        $this->smarty->clear_cache(null, $cacheGroup);
    }

    /**
     * Refresh timestamp of expires for a cachefile or cacheId
     *
     * @param int    $timestamp   New timestamp to expires
     * @param string $cachefile   Name of cache file or cache Id
     * @param string $tplFilename Optional name of template
     *
     * @return boolean Return true if action is performed
     */
    public function update($timestamp, $cachefile, $tplFilename = null)
    {
        // To understand this section it's necessary knowledge
        //  of smarty internals
        if (!is_null($tplFilename)) {

            // $cachefile is $cacheId if $tplFilenama isn't null
            $cachefile = $this->getCacheFileName($cachefile, $tplFilename);

            if (is_array($cachefile)) {
                $message = 'TemplateCacheManager::Update operation only '
                    .'supports one cache file at once.';
                throw new Exception($message);
            }
        }
        $cachefile = $this->cacheDir . $cachefile;

        if (file_exists($cachefile)) {

            // Get ctime of the file
            $ctime = filectime($cachefile);

            // Calculate the new expire time
            $expireTime = $timestamp - $ctime;

            // modify file contents with the new expireTime
            $cacheFileConents = file_get_contents($cachefile);
            $needle = '@\'cache_lifetime\'\ \=\>\ [0-9]{1,},@';
            $replace = '\'cache_lifetime\' => ' . $expireTime . ',';
            $contents = preg_replace($needle, $replace, $cacheFileConents);

            // write modified file contents
            file_put_contents($cachefile, $contents);

            return true;
        }

        return false;
    }

    /**
     * Fetch uri to update cache
     *
     * @param string $uri URI to fetch
     */
    public function fetch($uri)
    {
        // cURL Handle
        $ch = curl_init();

        // Options
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt(
            $ch,
            CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows; U; Windows NT 5.1; '
            .'pl; rv:1.9) Gecko/2008052906 Firefox/3.0'
        );
        ob_start();

        // Exec
        curl_exec($ch);
        ob_end_clean();
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return $httpCode == 200;
    }

    public function getResources($items = array())
    {
        $pk_contents = array();
        $pk_authors = array();
        foreach ($items as $item) {
            if (preg_match('/[0-9]{1,9}/', $item['resource'])) {
                $pk_contents[] = $item['resource'];
            } elseif (preg_match('/RSS([0-9]+)/', $item['resource'], $match)) {
                $pk_authors[] = $match[1];
            }
        }

        return array($pk_contents, $pk_authors);
    }

    protected function removeFile($filename)
    {


        if (file_exists($filename)) {
            unlink($filename);

            return true;
        }
    }

    public function dumpConfig()
    {

        $filename = $this->smarty->config_dir[0] . 'cache.conf';

        return parse_ini_file($filename, true);
    }

    public function saveConfig($config)
    {

        $filename = $this->smarty->config_dir[0] . 'cache.conf';
        $fp = @fopen($filename, 'w');

        if ($fp !== false) {
            foreach ($config as $section => $entry) {
                fputs($fp, '[' . $section . ']' . "\n");
                fputs($fp, 'caching = ' . $entry['caching'] . "\n");
                fputs($fp, 'cache_lifetime = '.$entry['cache_lifetime']."\n\n");
            }
            fclose($fp);
        } else {
            throw new Exception('Error open file: ' . $filename);
        }
    }
}
