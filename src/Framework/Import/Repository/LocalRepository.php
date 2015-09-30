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
        $this->syncPath = CACHE_PATH . DS .'importers';
        $this->compiler = new Compiler($this->syncPath);

        $this->contents = $this->compiler->getContentsFromCompiles();
    }

    /**
     * Gets an array of news from Efe
     *
     * @return array, the array of objects with news from Efe
     */
    public function countBy($criteria = [])
    {
        $files = array_filter($this->contents, function ($a) use ($criteria) {
            foreach ($criteria as $key => $value) {
                if (!property_exists($a, $key)
                    || (property_exists($a, $key)
                        && !preg_match('@' . $value . '@', $a->{$key}))
                ) {
                    return false;
                }
            }

            return true;
        });

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
        $files = array_filter($this->contents, function ($a) use ($criteria) {
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
     * @return array The list of resources.
     */
    public function findBy($criteria = [], $epp = 10, $page = 1)
    {
        $files = array_filter($this->contents, function ($a) use ($criteria) {
            foreach ($criteria as $key => $value) {
                if (!property_exists($a, $key)
                    || (property_exists($a, $key)
                        && !preg_match('@' . $value . '@', $a->{$key}))
                ) {
                    return false;
                }
            }

            return true;
        });

        $files = array_slice($files, $epp * ($page - 1), $epp);

        return $files;
    }
}
