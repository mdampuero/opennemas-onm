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

use Common\Core\Component\Template\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class CacheManager
{
    /**
     * Default cache values.
     *
     * @var array
     */
    protected $defaults = [];

    /**
     * The path to the configuration directory.
     *
     * @var string
     */
    protected $path;

    /**
     * The Template service.
     *
     * @var Template
     */
    protected $template = null;

    /**
     * Initializes the current CacheManager.
     *
     * @param Template $template The TemplateFactory service.
     */
    public function __construct(Template $template)
    {
        $this->fs       = new Filesystem();
        $this->template = $template;

        $this->defaults = [
            'frontpages' => [
                'cache_lifetime' => 600,
                'caching'        => 1,
                'name'           => _('Frontpage'),
            ],
            'frontpage-mobile'   => [
                'caching'        => 1,
                'cache_lifetime' => 600,
                'name'           => _('Frontpage mobile version'),
            ],
            'articles' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => _('Inner Article')
            ],
            'articles-mobile' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => _('Inner Article mobile version')
            ],
            'opinion' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => _('Inner Opinion')
            ],
            'video' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => ('Frontpage videos')
            ],
            'video-inner' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => ('Inner video')
            ],
            'gallery-frontpage' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => ('Gallery frontpage')
            ],
            'gallery-inner' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => ('Gallery Inner')
            ],
            'poll-frontpage' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => ('Polls frontpage')
            ],
            'poll-inner' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => ('Poll inner')
            ],
            'letter-frontpage' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => ('Letter frontpage')
            ],
            'letter-inner' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => ('Letter inner')
            ],
            'kiosko' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => ('Kiosko')
            ],
            'newslibrary' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => ('Newslibrary')
            ],
            'specials' => [
                'caching'        => 1,
                'cache_lifetime' => 86400,
                'name'           => ('Special')
            ],
            'sitemap' => [
                'cache_lifetime' => 300,
                'caching'        => 1,
                'name'           => ('Sitemap')
            ],
            'rss' => [
                'cache_lifetime' => 600,
                'caching'        => 1,
                'name'           => _('RSS')
            ]
        ];
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

        if (extension_loaded('Zend Opcache')) {
            opcache_reset();
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

        if (extension_loaded('Zend Opcache')) {
            opcache_reset();
        }

        return $this;
    }

    /**
     * Deletes all the cache files.
     *
     * @return CacheManager The current CacheManager.
     */
    public function deleteCompiles()
    {
        $this->template->clearCompiledTemplate();

        if (extension_loaded('Zend Opcache')) {
            opcache_reset();
        }

        return $this;
    }

    /**
     * Returns the configuration parameters.
     *
     * @return array The configuration.
     */
    public function read() : array
    {
        $path   = $this->path . '/cache.conf';
        $config = [];

        if ($this->fs->exists($path)) {
            $config = parse_ini_string($this->getFile($path)->getContents(), true);
        }

        $config = array_merge_recursive($this->defaults, $config);

        foreach ($config as &$value) {
            $value['caching']        = (int) $value['caching'];
            $value['cache_lifetime'] = (int) $value['cache_lifetime'];
        }

        return $config;
    }

    /**
     * Changes the path to directory where *.ini file is stored.
     *
     * @param string $path The path to the configuration directory.
     *
     * @return CacheManager The current service.
     */
    public function setPath(string $path) : CacheManager
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Saves the Smarty configuration to the configuration file.
     *
     * @param array $config The configuration to save.
     */
    public function write(array $config = [])
    {
        $path    = $this->path . '/cache.conf';
        $config  = array_merge_recursive($this->defaults, $config);
        $content = '';

        foreach ($config as $section => $entry) {
            $content .= '[' . $section . ']' . "\n"
                . 'caching = ' . $entry['caching'] . "\n"
                . 'cache_lifetime = ' . $entry['cache_lifetime'] . "\n\n";
        }

        $this->fs->dumpFile($path, $content);
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
     * Returns a File object from a path.
     *
     * @param string $path The path to the file.
     *
     * @return SplFileInfo The file.
     */
    protected function getFile(string $path) : SplFileInfo
    {
        return new SplFileInfo($path, $path, $path);
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
