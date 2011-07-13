<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Class Menu
 *
 * Class to manage frontpage menu in OpenNeMas
 * 
 */
class Menu
{
    public $pk_menu     = null;
    public $name        = null;
    public $type        = null;
    public $categories  = null;
    public $description = null;

    /**
     * Constructor
     *
     * @param int $id Privilege Id
    */
    function __construct($id=null)
    {
         
    }

    /**
     * Get a menu in the frontpage
     *
     * @param array $data image
     *
     * @return array with categories order by positions
     */

    public function getMenu($name)
    {
        $sql = "SELECT `categories` FROM menues WHERE name = '{$name}'";
        $rs = $GLOBALS['application']->conn->Execute( $sql );
 
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
 
        return unserialize($rs->fields['categories']);

    }

    /**
     * Update menu in the frontpage
     *
     * @param array
     *
     * @return bool if update ok true
     */
    public function setMenu($params = array())
    {
        $sql = "UPDATE menues SET  `categories`=?  ".
        		" WHERE `name`=? ";
 
        $values = array( serialize($params['categories']), $params['name'] );
 
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;                    
        }
      
        return true;

    }
    
}
