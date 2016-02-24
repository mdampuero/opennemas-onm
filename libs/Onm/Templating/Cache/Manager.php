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
     * List of cache groups parsed
     *
     * @var array
     **/
    public $cacheGroups = array();

    /**
     * Variable to store Smarty properties loaded from a Smarty cache file
     *
     * @var array
     **/
    public $properties = array();

    /**
     * Path to the smarty cache dir
     *
     * @var string
     **/
    protected $cacheDir = null;

    /**
     * Smarty instance used to interact with the cache layer
     *
     * @var Smarty
     **/
    protected $smarty = null;

    /**
     * Initializes the object instance, assigns the theme dir and smarty instance
     *
     * @param Smarty $smarty   Smarty class
     *
     * @return TemplateCacheManager the object initialized
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
     **/
    public function setSmarty($smarty)
    {
        if (!($smarty instanceof \Smarty)) {
            throw new \Exception('Please provide an Smarty object instance');
        }
        $this->smarty = $smarty;

        $this->cacheDir = $this->smarty->cache_dir;
    }


    /**
     * Scans the cache directory and returns an array with all cache files
     * that matches the filter
     *
     * @param  string $filter A regular expression for filtering cache file names
     *
     * @return array  Array of cache file names
     */
    public function scan($filter = null)
    {
        $caches  = array();
        $matches = array();
        $dirIt   = new \DirectoryIterator($this->cacheDir);
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
     * Get a exact name for a cache ID
     *
     * @see function scan
     * @param string $cacheId     Cache ID
     * @param string $tplFilename Template file name (sample: index.tpl)
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
     * Deletes a cache file physically
     *
     * @param string $cachefile   Cache file or cache Id
     * @param string $tplFilename Template file name
     *
     * @return boolean Return a boolean information of operation performed
     */
    public function delete($cachefile, $tplFilename = null)
    {
        // TODO: I think that this entire function could be done with Smarty::clearCache(null,'a|b|c')

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
     * Deletes all the caches of a group
     *
     * @param string $cacheGroup Name of group
     *
     * @return void
     */
    public function clearGroupCache($cacheGroup)
    {
        $this->smarty->clearCache(null, $cacheGroup);
    }

    /**
     * Removes a cache file given its full path
     *
     * @param string $filename the path of the file to remove
     *
     * @return boolean true if the file was deleted or it doesn't exists
     **/
    protected function removeFile($filename)
    {
        if (file_exists($filename)) {
            return unlink($filename);
        }

        return true;
    }
}
