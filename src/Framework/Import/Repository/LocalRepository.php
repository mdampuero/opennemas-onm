<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Repository;

use Framework\Import\Compiler\Compiler;

/**
 * Searches in the list of compilled contents.
 */
class LocalRepository
{
    /**
     * The contents in repository.
     *
     * @var array
     */
    public $contents = [];

    /**
     * The synchronization path.
     *
     * @var string
     */
    public $syncPath = '';

    /**
     * Initializes the LocalRepository.
     */
    public function __construct()
    {
        $this->syncPath = CACHE_PATH . DS . 'importers';
        $this->compiler = new Compiler($this->syncPath);

        $this->contents = $this->compiler->getContentsFromCompiles();

        usort($this->contents, function ($a, $b) {
            return $a->created_time < $b->created_time;
        });
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
     * Finds a Resource from a source given its id.
     *
     * @param string $source The source id.
     * @param string $id     The resource id.
     *
     * @return Resource The found resource.
     */
    public function find($source, $id)
    {
        $files = array_filter($this->contents, function ($a) use ($source, $id) {
            if ($a->source == $source && $a->id == $id) {
                return true;
            }

            return false;
        });

        if (empty($files)) {
            return null;
        }

        return array_pop($files);
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
     * Filters the list of contents given a criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return array The list of contents that match the criteria.
     */
    protected function filter($criteria)
    {
        $contents = array_filter($this->contents, function ($a) use ($criteria) {
            return preg_match('@' . $criteria['source'] . '@', $a->source);
        });

        // Remove source from criteria
        unset($criteria['source']);

        return array_filter($contents, function ($a) use ($criteria) {
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
}
