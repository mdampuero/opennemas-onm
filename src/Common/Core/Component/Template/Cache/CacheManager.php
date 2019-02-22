<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Template\Cache;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class CacheManager
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
        $this->smarty = $smarty;
        $this->finder = new Finder();
        $this->fs     = new Filesystem();
    }

    /**
     * Deletes a cache in file system given a cache id pattern and base tpl filename
     *
     * @param string $cacheId The cache id.
     *
     * @return CacheManager The current CacheManager.
     */
    public function delete($cacheId)
    {
        // Smarty convert the "|" character for a "^" character
        $cacheId = str_replace('|', '^', $cacheId);

        // Make a regular expression to filter
        $cacheId = '/^' . preg_quote($cacheId) . '\^.*?' . '/';

        $files = $this->finder
            ->in($this->smarty->getCacheDir())
            ->name($cacheId)
            ->files();

        foreach ($files as $file) {
            $this->deleteFile($file->getPathName());
        }

        return $this;
    }

    /**
     * Deletes all the cache files.
     *
     * @return CacheManager The current CacheManager.
     */
    public function deleteAll()
    {
        $this->smarty->clearAllCache();

        return $this;
    }

    /**
     * Removes a cache file  and cleans opcache internal cache.
     *
     * @param string $path The path to the file to remove.
     */
    protected function deleteFile($path)
    {
        if (!$this->fs->exists($path)) {
            return;
        }

        $this->fs->remove($path);

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($path, true);
        }
    }
}
