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
        
        $excludedFiles = self::getLocalFileList($this->syncPath);
        
        $synchronizer = new \Onm\Import\Synchronizer\FTP($params, $excludedFiles);
        $ftpSync = $synchronizer->downloadFilesToCacheDir($this->syncPath);

        return $ftpSync;
            
    }
    
    
    public function getSyncParams()
    {
        if (file_exists($this->syncFilePath)) {
            return unserialize(file_get_contents($this->syncFilePath));
        } else {
            return array(
                        'lastimport' => date('c'),
                        'imported_elements' => array(),
                        );
        }
    }
    
        
    public function updateSyncFile($importedElements = array())
    {

        $syncParams = $this->getSyncParams();
        
        if (!is_array($syncParams)) { $syncParams = array(); }
        
        if(!is_array($importedElements)
           && is_string($importedElements))
        {
            $importedElements = array($importedElements);
        }
        
        $elements = $this->getLocalFileList();
        $elementsCount = count($importedElements);
        for ($i=0; $i < $elementsCount; $i++) { 
            if(in_array($importedElements[$i], $elements)) {
                $elements []= $importedElements[$i];
            } else {
                
            }
        }
        
        $newSyncParams = array(
            'lastimport' => date('c'),
            'imported_elements' => $elementsImportedPresentInLocal,
        );
        $syncParams = array_merge($syncParams, $newSyncParams);
                
        $serializedParams = serialize($syncParams);
        file_put_contents($this->syncFilePath, $serializedParams);
        
        return $syncParams;
        
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
     * @return array, the array of news from EuropaPress
     * @author Fran Dieguez <fran@openhost.es>, 2011
     **/
    public function findAll($params = array())
    {
        
        //$synchronizer = new \Onm\Import\Synchronizer\FTP($this->syncPath);
        
        $files = $this->getLocalFileList($this->syncPath);
        
        $elements = array();
        $elementsCount = 0;
        foreach ($files as $file) {
            
            $element = new \Onm\Import\DataSource\Europapress($this->syncPath.DIRECTORY_SEPARATOR.$file);
            
            if ((($params['title'] != '*'))
                && !(preg_match('@'.$params['title'].'@', $element->title) > 0))
            {
                next;
            }
            
            if ((($params['category'] != '*'))
                && !(preg_match('@'.$params['category'].'@', $element->title) > 0))
            {
                next;
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
