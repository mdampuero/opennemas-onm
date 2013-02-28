<?php
/**
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
 * Class to import news from Efe Agency FTP
 *
 * @package    Onm_Import
 */
class FtpBasedAgencyImporter extends ImporterAbstract implements ImporterInterface
{
    // the instance object
    private static $instance = null;

    private $config = array();

    protected $lockFile = '';

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
     * gets an array of news from Efe
     *
     * @return array, the array of objects with news from Efe
     */
    public function findAll($params = array())
    {
        $filesSynced = $this->getLocalFileList($this->syncPath);
        rsort($filesSynced, SORT_STRING);

        $counTotalElements = count($filesSynced);
        if ($params['title'] == '*'
            && array_key_exists('items_page', $params)
            && array_key_exists('page', $params)
        ) {
            $files = array_slice(
                $filesSynced,
                $params['items_page'] * ($params['page']-1),
                $params['items_page']
            );
        } else {
            $files = $filesSynced;
        }
        unset($filesSynced);

        $elements = array();
        $elementsCount = 0;

        foreach ($files as $file) {

            $fileParts = explode('/', $file);
            $sourceId = (int) $fileParts[0];
            if (filesize($this->syncPath.DIRECTORY_SEPARATOR.$file) <= 0) {
                continue;
            }
            try {
                $element = DataSourceFactory::get($this->syncPath.DIRECTORY_SEPARATOR.$file);
            } catch (\Exception $e) {
                continue;
            }


            if ($params['title'] != '*'
                && !($element->hasContent($params['title']))
            ) {
                continue;
            }

            $category = $element->originalCategory;
            if ((($params['category'] != '*'))
                && !(preg_match('@'.$params['category'].'@', $category) > 0)
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
            $elementsCount++;
        }

        if ($params['title'] != '*') {
            $counTotalElements = $elementsCount;
        }

        // usort(
        //     $elements,
        //     create_function(
        //         '$a,$b',
        //         'return  $b->created_time->getTimestamp() '
        //         .'- $a->created_time->getTimestamp();'
        //     )
        // );

        return array($counTotalElements, $elements);
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
     * Fetches a DataSource\NewsMLG1 object from id
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
}
