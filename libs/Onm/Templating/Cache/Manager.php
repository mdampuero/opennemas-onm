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
namespace Onm\Templating\Cache;

/**
 * TemplateCacheManager class manage the smarty cache.
 *
 * @package Core
 */
class Manager
{
    /**
     * Smarty instance used to interact with the cache layer
     *
     * @var Smarty
     */
    protected $smarty = null;

    /**
     * Initializes the object instance, assigns the theme dir and smarty instance
     *
     * @param Smarty $smarty Smarty class
     */
    public function __construct($smarty = null)
    {
        if (!is_null($smarty)) {
            $this->setSmarty($smarty);
        }
    }

    /**
     * Sets the smarty object instance in the class
     *
     * @return Manager the object instance
     */
    public function setSmarty($smarty)
    {
        if (!($smarty instanceof \Smarty)) {
            throw new \Exception('Please provide an Smarty object instance');
        }
        $this->smarty = $smarty;

        return $this;
    }

    /**
     * Deletes all the caches of a group
     *
     * @param string $cacheGroup Name of group
     *
     * @return void
     */
    public function deleteGroup($cacheGroup)
    {
        $this->delete($cacheGroup);

        // Alternative: use the built in clear cache, less performant
        // $this->smarty->clearCache(null, $cacheGroup);

        return $this;
    }

    /**
     * Deletes all the caches
     *
     * @return void
     */
    public function deleteAll()
    {
        $this->smarty->clearAllCache();
    }

    /**
     * Deletes a cache in file system given a cache id pattern and base tpl filename
     *
     * @param string $cachefile   Cache file or cache Id
     * @param string $tplFilename Template file name
     *
     * @return boolean Return a boolean information of operation performed
     */
    public function delete($cacheId, $tplFilename = null)
    {
        $cacheFiles = $this->getMatchingCacheFileNames($cacheId, $tplFilename);

        $allDeleted = true;
        foreach ($cacheFiles as $filename) {
            $deleted = $this->removeFile($this->smarty->cache_dir.$filename);

            $allDeleted &= $deleted;
        }

        return $allDeleted;
    }

    /**
     * Returns the list of cache files that match a cacheID patther and/or
     * base tpl file name
     *
     * @param string $cacheId     Cache ID
     * @param string $tplFilename Template file name (sample: index.tpl)
     *
     * @return string Return a cache file name
     */
    public function getMatchingCacheFileNames($cacheId, $tplFilename = null)
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
        return $this->scan($filter);
    }

    /**
     * Scans the cache directory and returns an array with all cache files
     * that matches a provided regexp
     *
     * @param  string $filter A regular expression to filter cache file names
     *
     * @return array  Array of cache file names
     */
    public function scan($filter = null)
    {
        $caches  = array();
        $matches = array();
        $dirIt   = new \DirectoryIterator($this->smarty->cache_dir);
        foreach ($dirIt as $item) {
            if ($item->isDot()) {
                continue;
            }
            $filename = $item->current()->getFilename();

            if (empty($filter) || preg_match($filter, $filename, $matches)) {
                $caches[] = $filename;
            }
        }

        return $caches;
    }

    /**
     * Removes a cache file given its full path and cleans opcache/apc
     * internal cache
     *
     * @param string $filename the path of the file to remove
     *
     * @return boolean true if the file was deleted or it doesn't exists
     */
    protected function removeFile($filename)
    {
        if (file_exists($filename)) {
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($filename, true);
            } elseif (function_exists('apc_compile_file')) {
                apc_compile_file($filename);
            }

            return unlink($filename);
        }

        return true;
    }
}
