<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Repository;

use Common\Data\Serialize\Serializer\PhpSerializer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class LocalRepository
{
    /**
     * The list of contents in repository.
     *
     * @var array
     */
    protected $contents = [];

    /**
     * The Filesystem service
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * The path to load contents from.
     *
     * @var string
     */
    protected $path;

    /**
     * Initializes the Repository.
     */
    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    /**
     * Gets an array of news from Efe
     *
     * @return array, the array of objects with news from Efe
     */
    public function countBy($criteria = [])
    {
        $files = $this->filter($criteria);

        return count($files);
    }

    /**
     * Returns a resource or a list of resources.
     *
     * @param string $id The resource id or a list of resource ids.
     *
     * @return mixed A resource or a list of resources.
     */
    public function find($id)
    {
        $ids   = is_array($id) ? $id : [ $id ];
        $files = array_filter($this->contents, function ($a) use ($ids) {
            return in_array($a->id, $ids);
        });

        if (empty($files)) {
            return is_array($id) ? [] : null;
        }

        return is_array($id) ? $files : array_pop($files);
    }

    /**
     * Finds a list of resources given a criteria.
     *
     * @param array   $criteria The criteria.
     * @param integer $epp      The elements per page.
     * @param integer $page     The page.
     *
     * @return array The list of resources.
     */
    public function findBy($criteria = [], $epp = null, $page = 1)
    {
        $files = $this->filter($criteria);

        if (empty($epp)) {
            return $files;
        }

        return array_slice($files, $epp * ($page - 1), $epp);
    }

    /**
     * Reads contents from files found in path.
     *
     * @param string $path The path to read contents from.
     *
     * @return LocalRepository The current repository.
     */
    public function read(string $path) : LocalRepository
    {
        $files = $this->getFinder()->in($path)->name('/sync.*.*.php/')->files();

        foreach ($files as $file) {
            $this->contents = array_merge(
                $this->contents,
                PhpSerializer::unserialize($file->getContents())
            );
        }

        usort($this->contents, function ($a, $b) {
            return $a->priority !== $b->priority
                ? $a->priority >= $b->priority
                : $a->created_time < $b->created_time;
        });

        return $this;
    }

    /**
     * Removes the file specified by the path.
     *
     * @param string $path The path to the file to remove.
     *
     * @return LocalRepository The current repository.
     */
    public function remove(string $path) : LocalRepository
    {
        $directory = pathinfo($path)['dirname'];
        $pattern   = preg_replace('/\d+.php/', '*.php', basename($path));

        $files = $this->getFinder()->in($directory)
            ->name('/' . $pattern . '/')
            ->files();

        foreach ($files as $file) {
            $this->fs->remove($file);
        }

        return $this;
    }

    /**
     * Sets the list of contents in the current repository.
     *
     * @param array $contents The list of contents.
     *
     * @return LocalRepository The current repository.
     */
    public function setContents(array $contents) : LocalRepository
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Writes a list of contents to file.
     *
     * @param string $path The path to the file to write contents to.
     *
     * @return LocalRepository The current repository.
     */
    public function write(string $path) : LocalRepository
    {
        $this->fs->dumpFile($path, PhpSerializer::serialize($this->contents));

        return $this;
    }

    /**
     * Filters the list of contents given a criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return array The list of contents that match the criteria.
     */
    protected function filter($criteria)
    {
        $contents = array_filter($this->contents, function ($a) use ($criteria) {
            return preg_match('@' . $criteria['source'] . '@', $a->source)
                && (!array_key_exists('type', $criteria)
                    || preg_match('@' . $criteria['type'] . '@', $a->type));
        });

        // Remove source and type from criteria
        unset($criteria['source']);
        unset($criteria['type']);

        return array_filter($contents, function ($a) use ($criteria) {
            if (empty($criteria)) {
                return true;
            }

            foreach ($criteria as $key => $value) {
                // Force AND between tags in the same filter
                $pattern = strtolower(trim(preg_replace('/\s*,\s*/', '.*?', $value)));

                if (property_exists($a, $key)
                    && preg_match(
                        '@' . $pattern . '@',
                        strtolower($a->{$key})
                    )
                ) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Returns a new Finder.
     *
     * @return Finder The finder.
     */
    protected function getFinder()
    {
        return new Finder();
    }
}
