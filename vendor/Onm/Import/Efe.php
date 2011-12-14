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
 * Class to import news from Efe Agency FTP
 *
 * @package    Onm
 * @subpackage Import
 * @author     Fran Dieguez <fran@openhost.es>, 2011
 * @version    SVN: $Id: Efe.php 28842 Mér Xuñ 22 16:16:52 2011 frandieguez $
 */
class Efe implements \Onm\Import\Importer
{

    // the instance object
    static private $instance = null;

    // the configuration to access to the server
    private $defaultConfig = array(
                            'port' => 21,
                            );

    private $config = array();

    private $ftpConnection = null;

    public $syncPath = '';

    private $lockFile = '';


    /**
     * Ensures that we always get one single instance
     *
     * @return  object      the unique instance object
     *
     */
    static public function getInstance($config = array())
    {

        if (!(self::$instance instanceof self)
            //&& (count(array_diff($this->config, $config)) > 0)
            )
        {
            self::$instance = new self($config);
        }
        return self::$instance;

    }

    /**
     * Initializes the object and initializes configuration
     *
     * @return void
     *
     * @author Fran Dieguez <fran@openhost.es>
     */
    public function __construct($config = array())
    {

        $this->syncPath = implode(DIRECTORY_SEPARATOR,
                                  array(CACHE_PATH, 'efe_import_cache'));
        $this->syncFilePath = $this->syncPath.DIRECTORY_SEPARATOR.".sync";

        // Merging default configurations with new ones
        $this->config = array_merge($this->defaultConfig, $config);

        $this->lockFile = $this->syncPath.DIRECTORY_SEPARATOR.".lock";

    }

    /**
     * Creates the syncPath, to allow to work with it
     *
     * @param array     $params     the parameters to manipulate the behaviour of this function
     */
    public function setupSyncEnvironment($params = array())
    {
        if (!file_exists($this->syncPath)) {

            mkdir($this->syncPath);

        } elseif(!file_exists($this->syncFilePath)) {

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

        return (
                file_exists($this->syncFilePath)
                && is_writable($this->syncPath)
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
        if(!$this->isSyncEnrironmetReady()) {
            $this->setupSyncEnvironment();
        }

        if (file_exists($this->lockFile)) {
            throw new \Onm\Import\Synchronizer\LockException(sprintf(_("Seems that other user is syncing the news.")));
        }

        $this->lockSync();

        $excludedFiles = self::getLocalFileList($this->syncPath);

        $synchronizer = new \Onm\Import\Synchronizer\FTP($params);
        $ftpSync = $synchronizer->downloadFilesToCacheDir($this->syncPath, $excludedFiles);

        $this->unlockSync();

        return $ftpSync;

    }


    /*
     * Creates a lock for avoid concurrent sync by multiple users
     *
     * @return  void
     */
    public function lockSync()
    {
        try {
            touch($this->lockFile);
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
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
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
                'lastimport' => '',
                'imported_elements' => array(),
            );
        }
    }


    /**
     * Update statistics of synchronization file
     *
     * @param array|string importedElements, ids of new imported elements
     *
     * @return array, array('lastimport' => Date, 'imported_elements' => array())
     */
    public function updateSyncFile($importedElements = array())
    {

        $syncParams = $this->getSyncParams();

        if(is_string($importedElements)) {
            $importedElements = array($importedElements);
        }

        // Clean previously imported files that are not present in local cache
        $localElements = $this->getLocalFileList($this->syncPath);
        $previousImportedElements = $syncParams['imported_elements'];
        $previousImportedElementsCount = count($previousImportedElements);
        $elements = array();
        for ($i=0; $i < $previousImportedElementsCount; $i++) {
            if(in_array($previousImportedElements[$i], $localElements)) {
                $elements []= $previousImportedElements[$i];
            }
        }

        // Include new importedElements with old ones
        $newImportedelements = array_merge($importedElements,$elements);

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
     * @param array     $params     misc params that alteres function behaviour
     *
     * @return integer  minutes from last synchronization of elements
     */
    public function minutesFromLastSync($params = array())
    {
        $params = $this->getSyncParams();

        $to_time = strtotime(date('c'));
        $from_time = strtotime($params['lastimport']);

        return round((abs($to_time - $from_time) / 60), 0);


    }

    /**
     * gets an array of news from Efe
     *
     * @return array, the array of objects with news from Efe
     */
    public function findAll($params = array())
    {

        //$synchronizer = new \Onm\Import\Synchronizer\FTP($this->syncPath);

        $files = $this->getLocalFileList($this->syncPath);

        $elements = array();
        $elementsCount = 0;

        foreach ($files as $file) {

            if (filesize($this->syncPath.DIRECTORY_SEPARATOR.$file) <= 0) {
                continue;
            }
            try {
                $element = new \Onm\Import\DataSource\NewsMLG1($this->syncPath.DIRECTORY_SEPARATOR.$file);
            } catch (\Exception $e) {
                continue;
            }

            if ((($params['title'] != '*'))
                && !(preg_match('@'.strtolower($params['title']).'@', strtolower($element->title)) > 0))
            {
                continue;
            }

            if ((($params['category'] != '*'))
                && !(preg_match('@'.$params['category'].'@', $element->originalCategory) > 0))
            {
                continue;
            }

            if(array_key_exists('limit',$params)
               && ($elementsCount <= $params['limit']))
            {
                break;
            }

            $elements []= $element;
            $elementsCount++;

        }
        
        usort($elements, create_function('$a,$b', 'return  $b->created_time->getTimestamp() - $a->created_time->getTimestamp();'));

        return $elements;

    }


    /*
     * Fetches a DataSource\NewsMLG1 object from id
     *
     * @param $id
     *
     * @return  DataSource\Efe  the article object
     */
    public function findByID($id)
    {

        $element = new \Onm\Import\DataSource\NewsMLG1($this->syncPath.DIRECTORY_SEPARATOR.$id.'.xml');
        return  $element;

    }

    /*
     * Fetches a DataSource\NewsMLG1 object from id
     *
     * @param $fileName
     *
     * @return  DataSource\NewsMLG1  the article object
     */
    public function findByFileName($id)
    {

        $element = new \Onm\Import\DataSource\NewsMLG1($this->syncPath.DIRECTORY_SEPARATOR.$id);
        return  $element;

    }


    /**
     * gets a list of stored elements filtered by some params
     *
     * @param array     $params     array of params to filter elements with
     *
     * @return array    elements    stored
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
    static public function getLocalFileList($cacheDir)
    {
        $fileListing = glob($cacheDir.DIRECTORY_SEPARATOR.'*.xml');

        usort($fileListing, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));

        $fileListingCleaned = array();

        foreach($fileListing as $file) {
            $fileListingCleaned []= basename($file);
        }

        return $fileListingCleaned;
    }



}
