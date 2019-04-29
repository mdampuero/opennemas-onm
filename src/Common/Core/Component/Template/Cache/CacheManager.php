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

use Onm\Templating\Templating;

class CacheManager
{
    /**
     * The Template service.
     *
     * @var Template
     */
    protected $template = null;

    /**
     * Initializes the current CacheManager.
     *
     * @param Templating $template The Templating service.
     */
    public function __construct(Templating $template)
    {
        $this->template = $template;
    }

    /**
     * Deletes a cache in file system basing on the function arguments.
     *
     * @return CacheManager The current CacheManager.
     */
    public function delete()
    {
        // Do not add locale to the cache id
        $this->template->setLocale(false);

        // Get cache id basing on function arguments
        $cacheId = call_user_func_array(
            [ $this->template, 'getCacheId' ],
            func_get_args()
        );

        $this->template->setLocale(true);

        // Template converts "|" to "^"
        $cacheId = str_replace('|', '^', $cacheId);

        // Make a regular expression to filter
        $cacheId = '/^' . preg_quote($cacheId) . '\^.*?' . '/';

        $files = $this->getFiles($cacheId);

        foreach ($files as $file) {
            $this->deleteFile($file);
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
        $this->template->clearAllCache();

        return $this;
    }

    /**
     * Removes a cache file  and cleans opcache internal cache.
     *
     * @param string $path The path to the file to remove.
     *
     * @codeCoverageIgnore
     */
    protected function deleteFile($path)
    {
        if (!file_exists($path)) {
            return;
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($path, true);
        }

        unlink($path);
    }

    /**
     * Returns a list of files matching pattern.
     *
     * @param string $pattern The pattern to match.
     *
     * @return array The list of files.
     *
     * @codeCoverageIgnore
     */
    protected function getFiles($pattern = null)
    {
        $caches = [];
        $files  = new \DirectoryIterator($this->template->getCacheDir());

        foreach ($files as $file) {
            if ($files->isDot()) {
                continue;
            }

            if (empty($pattern)
                || preg_match($pattern, $file->current()->getFileName())
            ) {
                $caches[] = $file->current()->getPathName();
            }
        }

        return $caches;
    }
}
