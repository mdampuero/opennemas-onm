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
    * @author Fran Dieguez <fran@openhsot.es>
    **/
    static public function getInstance($config)
    {

        if ((!self::$instance instanceof self) or
            (count(array_diff($this->config, $config)) > 0))
        { 
            self::$instance = new self($config); 
        } 
        return self::$instance;

    }

    /**
    * initialized the object, ftp connection and initial configuration
    *
    * @return void
    * @author Fran Dieguez <fran@openhsot.es>
    **/
    public function __construct($config)
    {
        
        
        $this->syncPath = implode(DIRECTORY_SEPARATOR,
                                  array(CACHE_PATH, 'europapress_import_cache'));
        $this->syncFilePath = $this->syncPath.DIRECTORY_SEPARATOR.".sync";

        
        // Merging default configurations with new ones
        $this->config = array_merge($this->defaultConfig, $config);
        
        
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
        
        if ($this->connectToEuropaPressFTP() === true) {

            $ftpSync = \Onm\Import\Synchronizer\FTP::downloadFilesToCacheDir($this->ftpConnection, $this->syncPath);
            
        } else {
            //throw new \Onm\Import\SyncronizationException();
        }
        
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
     * Opens an FTP connection with the parameters of the object
     * 
     * @throws Exception, if something went wrong while connecting to FTP server
     */
    public function connectToEuropaPressFTP()
    {
        $this->ftpConnection = ftp_connect($this->config['server']);
        
        // test if the connection was successful
        if (!$this->ftpConnection) {
            throw new \Exception(sprintf(_('Can\'t connect to server %s'), $this->config['server']));
        } else {
        
            // if there is a ftp login configuration use it
            if (isset($this->config['user'])) {
        
                $loginResult = ftp_login($this->ftpConnection,
                                         $this->config['user'],
                                         $this->config['password']);
       
                if (!$loginResult) {
                    throw new \Exception(sprintf(_('Can\'t login into server '), $this->config['server']));
                }
                return true;
            }
            
        }
    }
    
    /**
     * gets an array of news from EuropaPress
     *
     * @return array, the array of news from EuropaPress
     * @author Fran Dieguez <fran@openhost.es>, 2011
     **/
    public function findAll($params = array())
    {
        if(is_null($this->ftpConnection)) {
            throw new \Exception(_('FTP connection not available.'));
        }
        
        $elements = array();
        
        $files = ftp_nlist($this->ftpConnection, ftp_pwd($this->ftpConnection));
        
        foreach($files as $file) {
            $elements []= $file;
            
        }
        
        /*var_dump(count($elements));
        die()*/;
        
        
        return $elements;
        
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
     * Return an array of localized categories
     * 
     * @param $arg
     */
    static public function getOriginalCategories()
    {
        return $original_categories =  array(
                                        'ECO' => _('Economy'),
                                        'MUNDO' => _('World'),
                                      );
    }
    
    /*
     * Retrieves a localized string of category from identifier
     * 
     * @param $arg
     */
    static public function matchCategoryName($categoryName)
    {
        if (empty($categoryName)) {
            throw new \ArgumentException;
        }
        
        $categories = self::getOriginalCategories();
        return $categories[$categoryName];
    }
    
    /*
     * Gets the minutes from last synchronization of elements
     * 
     * @param $params, misc params that alteres function behaviour
     * @return integer, minutes from last synchronization of elements
     */
    static public function minutesFromLastSync($params = array())
    {
        return 20;
    }

    
    
} // END class
