<?php
/**
 * Defines the Onm\Import\Repository\Europapress class
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

use \Onm\Import\DataSource\Europapress as EuropapressDataSource;

/**
 * Class to import news from EuropaPress Agency FTP
 *
 * @package    Onm_Import
 */
class Europapress extends ImporterAbstract implements ImporterInterface
{
    // the instance object
    private static $instance = null;

    // the configuration to access to the server
    private $defaultConfig = array(
        'port' => 21,
    );

    private $config = array();

    protected $lockFile = '';



    /**
     * Ensures that we always get one single instance
     *
     * @param array $config the configuration
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
     * @param array $config the configuration
     *
     * @return void
     */
    public function __construct($config = array())
    {
        $this->syncPath = implode(
            DIRECTORY_SEPARATOR,
            array(CACHE_PATH, 'europapress_import_cache')
        );
        $this->_syncFilePath = $this->syncPath.DIRECTORY_SEPARATOR.".sync";

        // Merging default configurations with new ones
        $this->config   = array_merge($this->defaultConfig, $config);

        $this->lockFile = $this->syncPath.DIRECTORY_SEPARATOR.".lock";
    }

    /**
     * gets an array of news from EuropaPress
     *
     * @param array $params the search criteria
     *
     * @return array, the array of objects with news from EuropaPress
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
                $file = $this->syncPath.DIRECTORY_SEPARATOR.$file;
                $element = new EuropapressDataSource($file);
            } catch (\Exception $e) {
                continue;
            }

            if (($params['title'] != '*')
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
     * Fetches a DataSource\Europapress object from id
     *
     * @param int $id
     *
     * @return DataSource\Europapress the article object
     */
    public function findByID($id)
    {
        $file    = $this->syncPath.DIRECTORY_SEPARATOR.$id.'.xml';
        $element = new EuropapressDataSource($file);

        return  $element;
    }

    /**
     * Fetches a DataSource\Europapress object from id
     *
     * @param string $fileName the file path
     *
     * @return DataSource\Europapress the article object
     */
    public function findByFileName($fileName)
    {
        $file    = $this->syncPath.DIRECTORY_SEPARATOR.$fileName;
        $element = new EuropapressDataSource($file);

        return  $element;
    }
}
