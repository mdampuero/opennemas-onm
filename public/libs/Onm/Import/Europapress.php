<?php
/**
 *  Copyright (C) 2011 by OpenHost S.L.
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 **/
/**
 * Class to import news from EuropaPress Agency FTP
 *
 * @package Import
 * @author Fran Dieguez <fran@openhost.es>, 2011
 **/
namespace Onm\Import;

class Europapress implements \Onm\Import\Importer
{

    // the instance object
    static private $instance = null;
    
    // the configuration to access to the server
    private $defaultConfig = array(
                            'port' => 21,
                            );
    
    private $config = array();
    
    private $ftpConnection = null;
    
    private $syncPath = '';
    
    private $lockFile = '';
    

    /**
    * Ensures that we always get one single instance
    *
    * @return object, the unique instance object 
    * @author Fran Dieguez <fran@openhost.es>
    **/
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
    * initialized the object and initial configuration
    *
    * @return void
    * @author Fran Dieguez <fran@openhost.es>
    **/
    public function __construct($config = array())
    {
        
        $this->syncPath = implode(DIRECTORY_SEPARATOR,
                                  array(CACHE_PATH, 'europapress_import_cache'));
        $this->syncFilePath = $this->syncPath.DIRECTORY_SEPARATOR.".sync";

        // Merging default configurations with new ones
        $this->config = array_merge($this->defaultConfig, $config);
        
        $this->lockFile = $this->syncPath.DIRECTORY_SEPARATOR.".lock";
        
    }
    
    /*
     * Creates the syncPath, to allow to work with it
     *
     * @param $params
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
    
    /*
     * Returns true if the syncPath exists and is writtable
     *
     * @return boolean, true if syncPath is present and writtable
     */
    public function isSyncEnrironmetReady()
    {
        
        return (
                file_exists($this->syncFilePath)
                && is_writable($this->syncPath)
                && is_writable($this->syncFilePath)
                );
    
    }
    
        
    /*
     * sync elements from news agency server and stores them into temporary dir
     * 
     * @param $params, misc params that alteres function behaviour
     * @return boolean, true if all goes well
     * @throws SyncronizationException, if something went wrong while sync 
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
     * @param $arg
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
     * @return array, array('lastimport' => Date, 'imported_elements' => array())
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
     * @param $params, misc params that alteres function behaviour
     * @return integer, minutes from last synchronization of elements
     */
    public function minutesFromLastSync($params = array())
    {
        $params = $this->getSyncParams();
        
        $to_time = strtotime(date('c'));
        $from_time = strtotime($params['lastimport']);
        
        return round((abs($to_time - $from_time) / 60), 0);
        
        
    }
    
    /**
     * gets an array of news from EuropaPress
     *
     * @return array, the array of objects with news from EuropaPress
     * @author Fran Dieguez <fran@openhost.es>, 2011
     **/
    public function findAll($params = array())
    {
        
        //$synchronizer = new \Onm\Import\Synchronizer\FTP($this->syncPath);
        
        $files = $this->getLocalFileList($this->syncPath);
        
        $elements = array();
        $elementsCount = 0;
        foreach ($files as $file) {
            
            try {
                $element = new \Onm\Import\DataSource\Europapress($this->syncPath.DIRECTORY_SEPARATOR.$file);
            } catch (\Exception $e) {
                continue;
            }
            
            if ((($params['title'] != '*'))
                && !(preg_match('@'.$params['title'].'@', $element->title) > 0))
            {
                continue;
            }
            
            if ((($params['category'] != '*'))
                && !(preg_match('@'.$params['category'].'@', $element->title) > 0))
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
     * gets the DataSource\Europapress object from id
     * 
     * @param $id
     */
    public function findByID($id)
    {
        
        $element = new \Onm\Import\DataSource\Europapress($this->syncPath.DIRECTORY_SEPARATOR.$id.'.xml');
        return  $element;
        
    }
    
    
    /**
     * gets a list of stored elements filtered by some params
     *
     * @param params, array of params to filter elements with
     * @return array, elements stored
     */
    public function findAllBy($params = array())
    {
        
    }
    
    

        /*
     * function 
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

    
    
} // END class
