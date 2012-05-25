<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Import;

use \Onm\Import\Synchronizer\LockException;
use \Onm\Import\DataSource\Europapress as EuropapressDataSource;

/**
 * Class to import news from EuropaPress Agency FTP
 *
 * @package    Onm
 * @subpackage Import
 */
class Europapress
    extends    ImporterAbstract
    implements ImporterInterface
{
    // the instance object
    private static $_instance = null;

    // the configuration to access to the server
    private $_defaultConfig = array(
        'port' => 21,
    );

    private $_config = array();

    protected $_lockFile = '';

    public $_syncPath = '';

    /**
     * Ensures that we always get one single instance
     *
     * @return object the unique instance object
     */
    public static function getInstance($config = array())
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($config);
        }

        return self::$_instance;
    }

    /**
     * Initializes the object and initializes configuration
     *
     * @return void
     */
    public function __construct($config = array())
    {
        $this->_syncPath = implode(DIRECTORY_SEPARATOR,
            array(CACHE_PATH, 'europapress_import_cache'));
        $this->_syncFilePath = $this->_syncPath.DIRECTORY_SEPARATOR.".sync";

        // Merging default configurations with new ones
        $this->_config   = array_merge($this->_defaultConfig, $config);

        $this->_lockFile = $this->_syncPath.DIRECTORY_SEPARATOR.".lock";
    }


    /**
     * gets an array of news from EuropaPress
     *
     * @return array, the array of objects with news from EuropaPress
     */
    public function findAll($params = array())
    {
        $filesSynced = $this->getLocalFileList($this->_syncPath);
        rsort($filesSynced, SORT_STRING);

        $counTotalElements = count($filesSynced);
        if (array_key_exists('items_page', $params)
            && array_key_exists('page', $params)
        ) {
            $files = array_slice($filesSynced,
                $params['items_page'] * ($params['page']-1),
                $params['items_page']);
        } else {
            $files = $filesSynced;
        }
        unset($filesSynced);

        $elements = array();
        $elementsCount = 0;

        foreach ($files as $file) {
            if (filesize($this->_syncPath.DIRECTORY_SEPARATOR.$file) <= 0) {
                continue;
            }
            try {
                $file = $this->_syncPath.DIRECTORY_SEPARATOR.$file;
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

        usort($elements, create_function('$a,$b',
            'return  $b->created_time->getTimestamp() '
                    .'- $a->created_time->getTimestamp();'));

        return array($counTotalElements, $elements);
    }


    /*
     * Fetches a DataSource\Europapress object from id
     *
     * @param $id
     *
     * @return DataSource\Europapress the article object
     */
    public function findByID($id)
    {
        $file    = $this->_syncPath.DIRECTORY_SEPARATOR.$id.'.xml';
        $element = new EuropapressDataSource($file);

        return  $element;
    }

    /*
     * Fetches a DataSource\Europapress object from id
     *
     * @param $fileName
     *
     * @return DataSource\Europapress the article object
     */
    public function findByFileName($id)
    {
        $file    = $this->_syncPath.DIRECTORY_SEPARATOR.$id;
        $element = new EuropapressDataSource($file);

        return  $element;
    }

}
