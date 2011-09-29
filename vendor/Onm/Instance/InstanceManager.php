<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Instance;
/**
 * Class for manage ONM instances.
 *
 * @package    Onm
 * @subpackage Instance
 * @author     Fran Dieguez <fran@openhost.es>
 * @version    Git: $Id: Settings.php Mér Xul 13 01:06:01 2011 frandieguez $
 */
class InstanceManager {
    
    /**
     * Fetches one onm instance from DB given a server name
     *
     *
     * @param string $serverName the domain name for one instance
     *
     * @return stdClass dummy object with properties for the loaded instance
     * @return false  if the instance doesn't exists
     */
    static public function load( $serverName )
    {
        $connection = self::getConnection();
        
        //TODO: improve search for allowing subdomains with wildcards
        $sql = "SELECT * FROM instances WHERE domains LIKE '%{$serverName}%' LIMIT 1";
        $rs = $connection->Execute($sql);
        
        if (!$rs) {
            $errorMsg = $connection->ErrorMsg();
            return false;
        }

        $instance = new \stdClass();
        foreach ($rs->fields as $key => $value ) {
            $instance->{$key} = $value;
        }
        $instance->settings = unserialize($instance->settings);
        foreach ($instance->settings as $key => $value ) {
            define($key, $value);
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
    static public function getListOfInstances()
    {
        
        $instances = array();
        $connection = self::getConnection();
        
                $sql = "SELECT * FROM instances";
        $rs = $connection->Execute($sql);
        
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
    static public function read($id)
    {
        
        $instances = array();
        $connection = self::getConnection();
        
        $sql = "SELECT * FROM instances WHERE id = ?";
        $rs = $connection->Execute($sql, array($id));
        
        if (!$rs) {
            $errorMsg = $connection->ErrorMsg();
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
    static public function changeActivated($id, $flag)
    {
        $connection = self::getConnection();
        
        $sql = "UPDATE instances SET activated = ? WHERE id = ?";
        $rs = $connection->Execute($sql, array($flag, $id));
        
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
    static public function update($data)
    {
        $connection = self::getConnection();
        
        $sql = "UPDATE instances SET name=?, domains=?, activated=?, settings=? WHERE id=?";
        $values = array(
            $data['name'],
            $data['internal_name'],
            $data['domains'],
            $data['activated'],
            $data['settings'],
            $data['id']
        );
        $rs = $connection->Execute($sql, $values);
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
    static public function delete($id)
    {
        $connection = self::getConnection();
        
        $sql = "DELETE FROM instances WHERE id=?";
        $rs = $connection->Execute($sql, array($id));
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
    static public function create($data)
    {
        $connection = self::getConnection();
        
        $sql = "INSERT INTO instances (name, internal_name, domains, activated, settings)
                VALUES (?, ?, ?, ?)";
        $values = array(
            $data['name'],
            $data['internal_name'],
            $data['domains'],
            $data['activated'],
            $data['settings'],
        );
        $rs = $connection->Execute($sql, $values);
        if (!$rs) {
            echo $connection->ErrorMsg();
            return false;
        }
        
        
        $connection2 = self::getConnection($data['settings']);
        $rs = $connection2->Execute("CREATE DATABASE {$data['settings']['BD_DATABASE']}");
        if (!$rs) {
            echo $connection2->ErrorMsg();
        }
        
        $exampleDatabasePath = realpath(APPLICATION_PATH.DS.'db'.DS.'instance-default.sql');
        $sql = file_get_contents($exampleDatabasePath);
        $rs = $connection2->Execute($sql);
        if (!$rs) {
            echo $connection2->ErrorMsg();
        }
        
        return true;
    }


}
