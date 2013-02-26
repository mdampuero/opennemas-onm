<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Import\Repository;

use \Onm\Import\DataSource\DataSourceFactory;

/**
 * Class to import news from Efe Agency FTP
 *
 * @package    Onm
 * @subpackage Import
 */
class Efe extends ImporterAbstract implements ImporterInterface
{
    // the instance object
    private static $instance = null;

    // the configuration to access to the server
    private $defaultConfig = array(
        'port' => 21,
    );

    private $config = array();

    protected $lockFile = '';

    public $syncPath = '';

    /**
     * Ensures that we always get one single instance
     *
     * @return object the unique instance object
     */
    public static function getInstance($config = array())
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * Initializes the object and initializes configuration
     *
     * @return void
     */
    public function __construct($config = array())
    {
        $this->syncPath = implode(
            DIRECTORY_SEPARATOR,
            array(CACHE_PATH, 'efe_import_cache')
        );
        $this->_syncFilePath = $this->syncPath.DIRECTORY_SEPARATOR.".sync";

        // Merging default configurations with new ones
        $this->config   = array_merge($this->defaultConfig, $config);

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
        if (array_key_exists('items_page', $params)
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

            if (array_key_exists('limit', $params)
               && ($elementsCount <= $params['limit'])
            ) {
                break;
            }

            $elements []= $element;
            $elementsCount++;
        }

        usort(
            $elements,
            create_function(
                '$a,$b',
                'return  $b->created_time->getTimestamp() '
                .'- $a->created_time->getTimestamp();'
            )
        );

        return array($counTotalElements, $elements);
    }

    /**
     * Fetches a DataSource\NewsMLG1 object from id
     *
     * @param $id
     *
     * @return DataSource\Efe the article object
     */
    public function findByID($id)
    {
        $file    = $this->syncPath.DIRECTORY_SEPARATOR.$id.'.xml';
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
    public function findByFileName($id)
    {
        $file    = $this->syncPath.DIRECTORY_SEPARATOR.$id;
        $element = DataSourceFactory::get($file);

        return  $element;
    }
}
