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
        $instanceValue = new \stdClass();
        
        $connection = self::getConnection();
        
        $sql = "SELECT * FROM instances WHERE domains LIKE '%{$serverName}%'";
        $rs = $connection->Execute($sql);
        
        if (!$rs) {
            $errorMsg = $connection->ErrorMsg();
        }
        
        foreach ($rs as $key) {
            $instanceValue->settings = unserialize($key['settings']);
            
            foreach ($instanceValue->settings as $key => $value ) {
                define($key, $value);
            }
        }

        return $instanceValue;
    }
    
    /*
     * Gets one Database connection
     * 
     * @param $arg
     */
    static public function getConnection()
    {
        // Database
        global $onmInstancesConnection;
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


}
