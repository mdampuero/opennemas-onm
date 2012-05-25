<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import;

/**
 * Class to import news from EuropaPress Agency FTP
 *
 * @package    Onm
 * @subpackage Import
 * @author     Fran Dieguez <fran@openhost.es>, 2011
 * @version    SVN: $Id: Europapress.php 28842 Mér Xuñ 22 16:16:52 2011 frandieguez $
 */
class Europapress implements \Onm\Import\Importer
{

    // the instance object
    private static $_instance = null;

    // the configuration to access to the server
    private $_defaultConfig = array('port' => 21);

    private $_config = array();

    private $_syncPath = '';

    private $_lockFile = '';


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
        $this->syncFilePath = $this->_syncPath.DIRECTORY_SEPARATOR.".sync";

        // Merging default configurations with new ones
        $this->_config   = array_merge($this->_defaultConfig, $config);

        $this->_lockFile = $this->_syncPath.DIRECTORY_SEPARATOR.".lock";
    }

    /**
     * Creates the syncPath, to allow to work with it
     *
     * @param array $params the parameters to manipulate
     *                      the behaviour of this function
     */
    public function setupSyncEnvironment($params = array())
    {
        if (!file_exists($this->_syncPath)) {
            mkdir($this->_syncPath);
        } elseif (!file_exists($this->syncFilePath)) {

            return touch($this->syncFilePath);
        }

        return false;
    }

    /**
     * Returns true if the syncPath exists and is writtable
     *
     * @return boolean true if syncPath is present and writtable
     */
    public function isSyncEnrironmetReady()
    {

        return (file_exists($this->syncFilePath)
            && is_writable($this->_syncPath)
            && is_writable($this->syncFilePath)
        );
    }


    /**
     * Sync elements from news agency server and stores them into temporary dir
     *
     * @param $params, misc params that alteres function behaviour
     *
     * @return boolean, true if all goes well
     *
     * @throws <b>SyncronizationException</b> if something went wrong while sync
     */
    public function sync($params = array())
    {
        // Check if the folder where store elements is ready and writtable
        if (!$this->isSyncEnrironmetReady()) {
            $this->setupSyncEnvironment();
        }

        if (file_exists($this->_lockFile)) {
            $message = sprintf(_("Seems that other user is syncing the news."));
            throw new \Onm\Import\Synchronizer\LockException($message);
        }

        $this->lockSync();

        $excludedFiles = self::getLocalFileList($this->_syncPath);

        $synchronizer = new \Onm\Import\Synchronizer\FTP($params);
        $ftpSync = $synchronizer->downloadFilesToCacheDir($this->_syncPath,
            $excludedFiles, $params['max_age']);

        $this->unlockSync();

        return $ftpSync;
    }


    /*
     * Creates a lock for avoid concurrent sync by multiple users
     *
     * @return void
     */
    public function lockSync()
    {
        try {
            touch($this->_lockFile);
        } catch (\Exception $e) {

            return;
        }
    }

    /*
     * Delete the lock for avoid concurrent sync by multiple users
     *
     * @return void
     */
    public function unlockSync()
    {
        if (file_exists($this->_lockFile)) {
            unlink($this->_lockFile);
        }
    }



    /**
     * Fetch the statistics of last synchronization
     *
     * @return array array('lastimport' => Date, 'imported_elements' => array())
     */
    public function getSyncParams()
    {
        if (file_exists($this->syncFilePath)) {
            return unserialize(file_get_contents($this->syncFilePath));
        } else {
            return array(
                'lastimport'        => '',
                'imported_elements' => array(),
            );
        }
    }


    /**
     * Update statistics of synchronization file
     *
     * @param array|string importedElements, ids of new imported elements
     *
     * @return array, array('lastimport' => Date,
     *                      'imported_elements' => array())
     */
    public function updateSyncFile($importedElements = array())
    {

        $syncParams = $this->getSyncParams();

        if (is_string($importedElements)) {
            $importedElements = array($importedElements);
        }

        // Clean previously imported files that are not present in local cache
        $localElements = $this->getLocalFileList($this->_syncPath);
        $previousImportedElements = $syncParams['imported_elements'];
        $previousImportedElementsCount = count($previousImportedElements);
        $elements = array();
        for ($i=0; $i < $previousImportedElementsCount; $i++) {
            if (in_array($previousImportedElements[$i], $localElements)) {
                $elements []= $previousImportedElements[$i];
            }
        }

        // Include new importedElements with old ones
        $newImportedelements = array_merge($importedElements, $elements);

        $newSyncParams = array(
            'lastimport' => date('c'),
            'imported_elements' => $newImportedelements,
        );

        file_put_contents($this->syncFilePath, serialize($newSyncParams));

        return $newSyncParams;

    }

    /*
     * Gets the minutes from last synchronization of elements
     *
     * @param array $params misc params that alteres function behaviour
     *
     * @return integer minutes from last synchronization of elements
     */
    public function minutesFromLastSync($params = array())
    {
        $params   = $this->getSyncParams();

        $toTime   = strtotime(date('c'));
        $fromTime = strtotime($params['lastimport']);

        return round((abs($toTime - $fromTime) / 60), 0);
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
                $file - $this->_syncPath.DIRECTORY_SEPARATOR.$file;
                $element = new \Onm\Import\DataSource\Europapress($file);
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
        $file = $this->_syncPath.DIRECTORY_SEPARATOR.$id.'.xml';
        $element = new \Onm\Import\DataSource\Europapress($file);

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
        $file = $this->_syncPath.DIRECTORY_SEPARATOR.$id;
        $element = new \Onm\Import\DataSource\Europapress($file);

        return  $element;
    }

    /**
     * gets a list of stored elements filtered by some params
     *
     * @param array $params array of params to filter elements with
     *
     * @return array elements    stored
     */
    public function findAllBy($params = array())
    {

    }

    /**
     * Fetches the files present in $cacheDir.
     *
     * @param string $cacheDir the directory where search files from.
     *
     * @return array the list of files
     */
    public static function getLocalFileList($cacheDir)
    {
        $fileListing = glob($cacheDir.DIRECTORY_SEPARATOR.'*.xml');

        usort($fileListing,
            create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));

        $fileListingCleaned = array();

        foreach ($fileListing as $file) {
            $fileListingCleaned []= basename($file);
        }

        return $fileListingCleaned;
    }
}
