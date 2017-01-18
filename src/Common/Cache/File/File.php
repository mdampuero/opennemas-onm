<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Cache\File;

use Common\Cache\Core\Cache;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * The File class provides methods to use file system based cache.
 */
class File extends Cache
{
    /**
     * Initializes the File cache.
     *
     * @param array $data The cache configuration.
     */
    public function __construct($data)
    {
        parent::__construct($data);

        if (empty($this->path) || empty($this->name)) {
            throw new \InvalidArgumentException();
        }

        // Force cache directory creation
        $this->getFileSystem();
    }

    /**
     * {@inheritdoc}
     */
    protected function contains($id)
    {
        return $this->getFinder()->name($id)->count() > 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function fetch($id)
    {
        $finder = $this->getFinder()->name($id)->files();
        $data   = null;

        foreach ($finder as $file) {
            $data = $this->getContent($file);
        }

        if (empty($data)) {
            return false;
        }

        return \unserialize($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchMulti($ids)
    {
        $data = [];

        foreach ($ids as $id) {
            $data[$this->getUnNamespacedId($id)] = $this->fetch($id);
        }

        return array_filter($data, function ($a) {
            return !empty($a);
        });
    }

    /**
     * Returns the file content.
     *
     * @param File $file The file object.
     *
     * @return string The file content.
     */
    protected function getContent($file)
    {
        return file_get_contents($file->getPathName());
    }

    /**
     * Returns a new Filesystem for cache.
     *
     * @return Filesystem The Filesystem component.
     */
    protected function getFileSystem()
    {
        $fs = new Filesystem();
        $path = $this->path;

        if (!$fs->exists($path)) {
            $fs->mkdir($path, 0775);
        }

        return $fs;
    }

    /**
     * Returns a new Finder in the configured paths.
     *
     * @return Finder A Finder component.
     */
    protected function getFinder()
    {
        $finder = new Finder();

        $finder->in($this->path);

        return $finder;
    }

    /**
     * {@inheritdoc}
     */
    protected function delete($id)
    {
        $finder = $this->getFinder()->name($id)->files();

        foreach ($finder as $file) {
            $this->getFileSystem()->remove($file->getPathName());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function deleteByPattern($pattern)
    {
        $this->delete($pattern);
    }

    /**
     * {@inheritdoc}
     */
    protected function deleteMulti($ids)
    {
        foreach ($ids as $id) {
            $this->remove($id);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function save($id, $data, $ttl = null)
    {
        $path = $this->path . DS . $id;
        $this->getFileSystem()->dumpFile($path, serialize($data));
    }

    /**
     * {@inheritdoc}
     */
    protected function saveMulti($data)
    {
        foreach ($data as $key => $value) {
            $this->save($key, $value);
        }
    }
}
