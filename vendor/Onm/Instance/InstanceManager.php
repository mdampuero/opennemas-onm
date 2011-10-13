<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Instance;
use \FilesManager as fm;
/**
 * Class for manage ONM instances.
 *
 * @package    Onm
 * @subpackage Instance
 * @author     Fran Dieguez <fran@openhost.es>
 * @version    Git: $Id: Settings.php Mér Xul 13 01:06:01 2011 frandieguez $
 */
class InstanceManager
{
    
    private $_connnection = null;
    
    /**
     * @var Onm\Request Singleton instance
     */
    protected static $_instance;
    
    /*
     * Initializes the Request object
     * 
     */
    public function __construct()
    {
        $this->_connection = self::getConnection();
        return $this;
    }
    
    /**
     * Retrieve singleton instance
     *
     * @return Zend_Loader_Autoloader
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Fetches one onm instance from DB given a server name
     *
     *
     * @param string $serverName the domain name for one instance
     *
     * @return stdClass dummy object with properties for the loaded instance
     * @return false  if the instance doesn't exists
     */
    public function load( $serverName )
    {
        //TODO: improve search for allowing subdomains with wildcards
        $sql = "SELECT * FROM instances WHERE domains LIKE '%{$serverName}%' LIMIT 1";
        $rs = $this->_connection->Execute($sql);
        
        if (!$rs) {
            $errorMsg = $this->_connection->ErrorMsg();
            return false;
        }
        
        if (preg_match("@manager@", $_SERVER["PHP_SELF"])) {
            $instance = new \stdClass();
            $instance->interna_name = 'onm_manager';
            $instance->activated = true;
            $configs = array(
                'INSTANCE_UNIQUE_NAME' => $instance->interna_name,
                'MEDIA_URL' => '',
                'TEMPLATE_USER' => '',
            );
            foreach ($configs as $key => $value) {
                define($key, $value);
            }
            
            return $instance;
        }

        //If found matching instance initialize its contants and return it
        if ($rs->fields) {
            
            $instance = new \stdClass();
            foreach ($rs->fields as $key => $value ) {
                $instance->{$key} = $value;
            }
            define('INSTANCE_UNIQUE_NAME', $instance->internal_name);
            
            // Transform all the intance settings into application constants.
            $instance->settings = unserialize($instance->settings);
            if (empty($instance->settings['MEDIA_URL']))
            {
                $instance->settings['MEDIA_URL'] = implode(
                    '/',
                    array(
                        'http://',
                        $_SERVER['HTTP_HOST'],
                        'media'.
                        '/'
                    )
                );
            }
            foreach ($instance->settings as $key => $value ) {
                define($key, $value);
            }
            
            // If this instance is not activated throw an exception
            if ($instance->activated != '1') {
                throw new \Onm\Instance\NotActivatedException(_('Instance not activated'));
            }
        
        // If this instance doesn't exist check if the request is from manager
        // in that case return a dummie instance.
        } else {
            
            throw new \Onm\Instance\NotFoundException(_('Instance not found'));
            
        }
        return $instance;
        
    }
    
    /*
     * Gets one Database connection
     * 
     * @param $arg
     */
    static public function getConnection($connectionData=null)
    {
        // Database
        global $onmInstancesConnection;
        if (
            !is_null($connectionData)
            && is_array($connectionData)
        ) {
            $onmInstancesConnection = $connectionData;
        }
        $conn = \ADONewConnection($onmInstancesConnection['BD_TYPE']);
        $conn->Connect(
            $onmInstancesConnection['BD_HOST'],
            $onmInstancesConnection['BD_USER'],
            $onmInstancesConnection['BD_PASS'],
            $onmInstancesConnection['BD_DATABASE']
        );

        // Check if adodb is log enabled
        $conn->LogSQL();
        return $conn;
    }
    
    /*
     * Gets a list of instances
     * 
     */
    public function getListOfInstances()
    {
        
        $instances = array();
        
        $sql = "SELECT * FROM instances";
        $rs = $this->_connection->Execute($sql);
        
        if (!$rs) {
            $errorMsg = $connection->ErrorMsg();
            return false;
        }
        
        foreach ($rs as $key) {
            $instance = new \stdClass();
            $instance->id = $key["id"];
            $instance->name = $key["name"];
            $instance->activated = $rs->fields["activated"];
            $instance->domains = $key["domains"];
            $instance->settings = unserialize($key['settings']);
            $instances []= $instance;
        }

        return $instances;
    }
    
    /*
     * Gets one instances
     * 
     */
    public function read($id)
    {
        
        $instances = array();
        
        $sql = "SELECT * FROM instances WHERE id = ?";
        $rs = $this->_connection->Execute($sql, array($id));
        
        if (!$rs) {
            $errorMsg = $rs->ErrorMsg();
            return false;
        }

        $instance = new \stdClass();
        foreach ($rs->fields as $key => $value ) {
            $instance->{$key} = $value;
        }
        $instance->settings = unserialize($instance->settings);
        
        return $instance;
    }
    
    /*
     * Change activated flag for one instance given its id
     * 
     * @param $id
     */
    public function changeActivated($id, $flag)
    {
        $sql = "UPDATE instances SET activated = ? WHERE id = ?";
        $rs = $this->_connection->Execute($sql, array($flag, $id));
        
        if (!$rs) {
            $errorMsg = $connection->ErrorMsg();
            return false;
        }
        return true;
    }
    
    
    /*
     * update
     * 
     * @param $data
     */
    public function update($data)
    {
        $sql = "UPDATE instances SET name=?, internal_name=?, domains=?, activated=?, settings=? WHERE id=?";
        $values = array(
            $data['name'],
            $data['internal_name'],
            $data['domains'],
            $data['activated'],
            serialize($data['settings']),
            $data['id']
        );
        
        $rs = $this->_connection->Execute($sql, $values);
        if (!$rs) {
            $errorMsg = $connection->ErrorMsg();
            return false;
        }
        return true;
    }
    
    /*
     * Deletes one instance given its id
     * 
     * @param $id
     */
    public function delete($id)
    {
        $sql = "DELETE FROM instances WHERE id=?";
        $rs = $this->_connection->Execute($sql, array($id));
        if (!$rs) {
            $errorMsg = $this->_connection->ErrorMsg();
            return false;
        }
        return true;
    }
    
    /*
     * Deletes one instance given its id
     * 
     * @param $id
     */
    public function create($data)
    {
        $sql = "INSERT INTO instances (name, internal_name, domains, activated, settings)
                VALUES (?, ?, ?, ?, ?)";
        $values = array(
            $data['name'],
            $data['internal_name'],
            $data['domains'],
            $data['activated'],
            serialize($data['settings']),
        );
        
        $rs = $this->_connection->Execute($sql, $values);
        if (!$rs) {
            echo $this->_connection->ErrorMsg();
            return false;
        }
        
        global $onmInstancesConnection;
        $conn = \ADONewConnection($onmInstancesConnection['BD_TYPE']);
        $conn->Connect(
            $onmInstancesConnection['BD_HOST'],
            $onmInstancesConnection['BD_USER'],
            $onmInstancesConnection['BD_PASS']
        );
        
        $rs = $conn->Execute("CREATE DATABASE `{$data['settings']['BD_DATABASE']}`");
        
        if ($rs) {
            
            
            $connection2 = self::getConnection($data['settings']);
            $exampleDatabasePath = realpath(APPLICATION_PATH.DS.'db'.DS.'instance-default.sql');
            $execLine = "mysql -h {$onmInstancesConnection['BD_HOST']} -u {$onmInstancesConnection['BD_USER']} -p{$onmInstancesConnection['BD_PASS']} {$data['settings']['BD_DATABASE']} < {$exampleDatabasePath}";
            exec($execLine);
            
            $mediaPath = SITE_PATH.DS.'media'.DS.$data['internal_name'];
            if (!file_exists($mediaPath)) {
                fm::recursiveCopy(SITE_PATH.DS.'media'.DS.'default', $mediaPath);
            }   
        } else {
            return false;
        }
        
        return true;
    }
    
    /*
     * Returns true if the current loaded instance is activated
     * 
     */
    public function isActivated()
    {
        return ($this->activated == 1);
    }


}
