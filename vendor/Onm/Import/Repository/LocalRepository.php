<?php
/**
 * Defines the Onm\Import\Repository class
 *
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_Import
 **/
namespace Onm\Import\Repository;

use \Onm\Import\DataSource\DataSourceFactory;

/**
 * Class to import news from any news Agency FTP / HTTP
 *
 * @package    Onm_Import
 */
class LocalRepository
{
    // the instance object
    private static $instance = null;

    private $config = array();

    public $syncPath = '';

    /**
     * Initializes the object and initializes configuration
     *
     * @return void
     */
    public function __construct($config = array())
    {
        $this->syncPath = implode(
            DIRECTORY_SEPARATOR,
            array(CACHE_PATH, 'importers')
        );
        $this->syncFilePath = $this->syncPath.DIRECTORY_SEPARATOR.".sync";

        $this->lockFile = $this->syncPath.DIRECTORY_SEPARATOR.".lock";
    }

    /**
     * Gets an array of news from Efe
     *
     * @return array, the array of objects with news from Efe
     */
    public function findAll($params = array())
    {
        $filesSynced = \Onm\Import\Synchronizer\Synchronizer::getLocalFileList($this->syncPath);

        $elements = array();
        foreach ($filesSynced as $file) {
            $fileParts = explode('/', $file);
            $sourceId = (int) $fileParts[0];

            if ($params['source'] != '*'
                && $sourceId != $params['source']
            ) {
                continue;
            }

            $filePath = $this->syncPath.DIRECTORY_SEPARATOR.$file;
            if (filesize($filePath) <= 0) {
                continue;
            }
            try {
                $element = DataSourceFactory::get($filePath);
            } catch (\Exception $e) {
                continue;
            }

            if (is_null($element)) {
                continue;
            }

            if ($params['title'] != '*'
                && !($element->hasContent($params['title']))
            ) {
                continue;
            }

            if ($params['title'] == '*'
                && array_key_exists('limit', $params)
                && ($elementsCount <= $params['limit'])
            ) {
                break;
            }

            $element->source_id = $sourceId;

            $elements []= $element;
        }

        $counTotalElements = count($elements);
        if (array_key_exists('items_page', $params)
            && array_key_exists('page', $params)
        ) {
            $files = array_slice(
                $elements,
                $params['items_page'] * ($params['page']-1),
                $params['items_page']
            );
        } else {
            $files = $elements;
        }

        return array($counTotalElements, $files);
    }

    /**
     * Fetches a DataSource\NewsMLG1 object from id
     *
     * @param $id
     *
     * @return DataSource\Efe the article object
     */
    public function findByID($sourceId, $id)
    {
        $file    = $this->syncPath.DIRECTORY_SEPARATOR.$sourceId.DIRECTORY_SEPARATOR.$id.'.xml';

        if (!realpath($file)) {
            throw new \Exception();
        }
        $element = DataSourceFactory::get($file);

        return  $element;
    }

    /**
     * Fetches a DataSource\NewsMLG1 object from file name
     *
     * @param $fileName
     *
     * @return DataSource\NewsMLG1 the article object
     */
    public function findByFileName($sourceId, $id)
    {
        $file    = $this->syncPath.DIRECTORY_SEPARATOR.$sourceId.DIRECTORY_SEPARATOR.$id;

        if (!realpath($file)) {
            throw new \Exception();
        }

        $element = DataSourceFactory::get($file);

        return  $element;
    }

    /**
     * Removes the local files for a given source id
     *
     * @return boolean true if the files were deleted
     * @throws Exception If the files weren't deleted
     **/
    public function deleteFilesForSource($id)
    {
        $path = realpath($this->syncPath.DIRECTORY_SEPARATOR.$id);

        if (!empty($path)) {
            return \FilesManager::deleteDirectoryRecursively($path);
        }
        return false;
    }
}
