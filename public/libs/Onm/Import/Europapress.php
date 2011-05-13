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
 * Class for import news from EuropaPress Agency FTP
 *
 * @package Import
 * @author Fran Dieguez <fran@openhost.es>, 2011
 **/
class Onm_Import_Europapress implements Onm_Import_Importer
{
    
    // the instance object
    static $instance = null;
    
    // the configuration to access to the server
    private $defaultConfig = array(
                            'port' => 21,
                            );
    
    private $config = array();
    
    private $ftpConnection = null;
    
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
        
        // Merging default configurations with new ones
        $this->config = array_merge($this->defaultConfig, $config);
        
        $this->ftpConnection = ftp_connect($this->config['server']);
        
        // test if the connection was successful
        if (!$this->ftpConnection) {
            throw new Exception('Can\'t connect to server '.$this->config['server']);
        } else {
            
            // if there is ftp login configuration use it
            if (isset($this->config['user'])) {
                
                $loginResult = ftp_login($this->ftpConnection,
                                         $config['user'],
                                         $config['password']);
                
                if (!$loginResult) {
                    throw new Exception('Can\'t login into server '.$this->config['server']);
                }
            }
            
        }
    }
    
    /**
    * gets an array of news from EuropaPress
    *
    * @return array, the array of news from EuropaPress
    * @author Fran Dieguez <fran@openhost.es>, 2011
    **/
    public function findAll()
    {
        $elements = array();
        
        $files = ftp_nlist($this->ftpConnection, ftp_pwd($this->ftpConnection));
        
        foreach($files as $file) {
            $elements = '';
            
        }
        
        var_dump($elements);
        die();
        
        
        return $elements;
        
    }
    
    public function findAllBy($params)
    {
        
    }
    
    
    
} // END class
