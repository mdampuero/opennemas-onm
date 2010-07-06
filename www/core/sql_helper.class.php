<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @package    Core
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * SqlHelper
 * 
 * @package    Core
 * @subpackage Utils
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: PHP-1.php 1 2009-11-25 11:37:15Z vifito $
 */
class SqlHelper
{    
    /**
     * Build "update" query using $fields array to write "set" sentence
     *
     * @version 20100514142017
     * @throws SqlHelperException
     * @throws Exception
     * @see SqlHelper::bindAndUpdate
     * @param string $table
     * @param array $fields Array with name of fields to update ($colname => $value)
     * @param string $filter String for where sentece
     * @param object|null $conn ADOConnection instance
     */
    public function update($table, $fields, $filter, $conn=null)
    {
        $sql = 'UPDATE `%s` SET %s WHERE %s';
        
        $set = array();
        $values = array();
        foreach($fields as $k => $field) {
            $set[]    = '`' . $k . '` = ?';
            $values[] = $field;
        }
        
        $sql = sprintf($sql, $table, implode(', ', $set), $filter);
        
        if(is_null($conn)) {
            if( Zend_Registry::isRegistered('conn') ) {
                $conn = Zend_Registry::get('conn');
            } else {
                throw new Exception('Zend_Registry not found entry: "conn".');
            }
        }
        
        if($conn->Execute($sql, $values) === false) {
            $error_msg = $conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            throw new SqlHelperException($error_msg);
        }
    }
    
    /**
     * Search into $data values that match with keys into $fields to build
     * new array for use SqlHelper::update
     * Also check if values isset and not empty
     *
     * <code>
     *  $filter = '`pk_content` = ' . $pk_content;
     *  $fields = array('starttime', 'endtime', 'content_status', 'available',
     *                   'fk_user_last_editor', 'frontpage', 'in_home', 'permalink');
     *  SqlHelper::bindAndUpdate('contents', $fields, $_POST, $filter);
     * </code>
     *
     * @version 20100514142017
     * @throws SqlHelperException
     * @throws Exception
     * @uses SqlHelper::update
     * @param string $table
     * @param array $fields Array with name of fields to update
     * @param array $data Array keyField => valueField, equals to POST
     * @param string $filter String for where sentece
     * @param object|null $conn ADOConnection instance 
     */
    public function bindAndUpdate($table, $fields, $data, $filter, $conn=null)
    {
        $merged = array();
        foreach($fields as $field) {
            if(isset($data[$field])) {
                $merged[ $field ] = $data[$field];
            }
        }
        
        SqlHelper::update($table, $merged, $filter, $conn);
    }
    
    /**
     * Build "insert" query using $fields array to write "values" sentence
     *
     * @version 20100514142017
     * @throws SqlHelperException
     * @throws Exception
     * @see SqlHelper::bindAndUpdate
     * @param string $table
     * @param array $fields Array with name of fields to update ($colname => $value)
     * @param object|null $conn ADOConnection instance
     * @return int|boolean  Insert ID or false
     */
    public function insert($table, $fields, $conn=null)
    {
        $sql = 'INSERT INTO `%s` (%s) VALUES (%s)';
        
        $set = array();
        $values = array();
        foreach($fields as $k => $field) {
            $set[]    = '`' . $k . '`';
            $values[] = $field;
        }
        
        $marks = implode(', ', array_fill(0, count($set), '?'));
        $sql = sprintf($sql, $table, implode(', ', $set), $marks);
        
        if(is_null($conn)) {
            if( Zend_Registry::isRegistered('conn') ) {
                $conn = Zend_Registry::get('conn');
            } else {
                throw new Exception('Zend_Registry not found entry: "conn".');
            }
        }
        
        if($conn->Execute($sql, $values) === false) {
            $error_msg = $conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            throw new SqlHelperException($error_msg);
        }
        
        $id = $conn->Insert_ID();
        
        return $id;
    }
    
    /**
     * Search into $data values that match with keys into $fields to build
     * new array for use SqlHelper::insert
     * Also check if values isset and not empty
     *
     * <code>
     *  $fields = array('starttime', 'endtime', 'content_status', 'available',
     *                   'fk_user_last_editor', 'frontpage', 'in_home', 'permalink');
     *  SqlHelper::bindAndInsert('contents', $fields, $_POST);
     * </code>
     *
     * @version 20100514142017
     * @throws SqlHelperException
     * @throws Exception
     * @uses SqlHelper::insert
     * @param string $table
     * @param array $fields Array with name of fields to update
     * @param array $data Array keyField => valueField, equals to POST
     * @param object|null $conn ADOConnection instance
     * @return int|boolean  Insert ID or false
     */
    public function bindAndInsert($table, $fields, $data, $conn=null)
    {
        $merged = array();
        foreach($fields as $field) {
            if(isset($data[$field])) {
                $merged[ $field ] = $data[$field];
            }
        }
        
        return SqlHelper::insert($table, $merged, $conn);
    }
    
} // END: class SqlHelper