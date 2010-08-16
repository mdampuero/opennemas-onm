<?php
/**
 * ZFDebug Zend Additions
 *
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 * @version    $Id: Database.php 74 2009-05-19 12:30:36Z gugakfugl $
 */

/**
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 */
class ZFDebug_Controller_Plugin_Debug_Plugin_Adodb extends ZFDebug_Controller_Plugin_Debug_Plugin implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{

    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'adodb';

    /**
     * @var array
     */
    protected $_db = null;
    
    public static $execSql = array();
    public static $totalExecSql = 0;
    
    public static $execCacheSql = array();
    public static $totalExecCacheSql = 0;

    /**
     * Create ZFDebug_Controller_Plugin_Debug_Plugin_ADOdb
     *
     * @param Zend_Db_Adapter_Abstract|array $adapters
     * @return void
     */
    public function __construct(array $options = array())
    {
        $this->_db = Zend_Registry::get('conn');
        $this->_db->LogSQL();
        
        $this->_db->fnExecute = 'ZFDebug_CountExecs';
        $this->_db->fnCacheExecute = 'ZFDebug_CountCachedExecs';
    }

    /**
     * Gets identifier for this plugin
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * Gets menu tab for the Debugbar
     *
     * @return string
     */
    public function getTab()
    {
        return ' ADOdb (' . (self::$totalExecSql + self::$totalExecCacheSql) . ')';
    }        

    /**
     * Gets content panel for the Debugbar
     *
     * @return string
     */
    public function getPanel()
    {
        $html = '<div id="ZFDebug_adodb-tabs">
            <ul>
                <li><a href="#ZFDebug_adodb-sql">SQL</a></li>
                <li><a href="#ZFDebug_adodb-invalid">Invalid SQL</a></li>
                <li><a href="#ZFDebug_adodb-expensive">Expensive SQL</a></li>
                <li><a href="#ZFDebug_adodb-suspicious">Suspicious SQL</a></li>
                <li><a href="#ZFDebug_adodb-healthcheck">Health check</a></li>
            </ul>';
        
        $perf = NewPerfMonitor($this->_db);
        
        $html .= '<div id="ZFDebug_adodb-sql">' . $this->_renderSql() . '</div>';
        $html .= '<div id="ZFDebug_adodb-invalid">' . $perf->InvalidSQL(10) . '</div>';
        $html .= '<div id="ZFDebug_adodb-expensive">' . $perf->ExpensiveSQL(10) . '</div>';
        $html .= '<div id="ZFDebug_adodb-suspicious">' . $perf->SuspiciousSQL(10) . '</div>';
        $html .= '<div id="ZFDebug_adodb-healthcheck">' . $perf->HealthCheck() . '</div>';
        
        $html .= '</div>';
        
        $html .= '<script>$(function(){$("#ZFDebug_adodb-tabs").tabs();});</script>';
        
        return $html;
    }

    private function _renderSql()
    {
        $html = '';
        
        $queries = ZFDebug_Controller_Plugin_Debug_Plugin_Adodb::$execSql;
        
        $html .= '<h5>Total queries <strong>' .
            ZFDebug_Controller_Plugin_Debug_Plugin_Adodb::$totalExecSql .
            '</strong>.</h5>';
        $html .= '<ol>';
        foreach($queries as $q) {
            $html .= '<li>' . htmlentities($q, ENT_COMPAT, 'UTF-8') . '</li>';
        }
        $html .= '</ol>';
        
        $queries = ZFDebug_Controller_Plugin_Debug_Plugin_Adodb::$execCacheSql;
        
        $html .= '<h5>Total queries <strong>' .
            ZFDebug_Controller_Plugin_Debug_Plugin_Adodb::$totalExecCacheSql .
            '</strong> cached.</h5>';
        $html .= '<ol>';
        foreach($queries as $q) {
            $html .= '<li>' . htmlentities($q, ENT_COMPAT, 'UTF-8') . '</li>';
        }
        $html .= '</ol>';
        
        return $html;
    }
    
    
}

// Callbacks to ADOdb
if(!function_exists('ZFDebug_CountExecs')) {
    function ZFDebug_CountExecs($db, $sql, $inputarray)
    {
        $total = ZFDebug_Controller_Plugin_Debug_Plugin_Adodb::$totalExecSql;
        
        if (!is_array(inputarray)) {
            $total++;
        } elseif (is_array(reset($inputarray))) {
            $total += sizeof($inputarray);
        } else {
            $total++;
        }
        
        ZFDebug_Controller_Plugin_Debug_Plugin_Adodb::$totalExecSql = $total;
        ZFDebug_Controller_Plugin_Debug_Plugin_Adodb::$execSql[] = $sql;        
        
        return null;
    }
    
    function ZFDebug_CountCachedExecs($db, $secs2cache, $sql, $inputarray)
    {
        ZFDebug_Controller_Plugin_Debug_Plugin_Adodb::$totalExecCacheSql++;
        ZFDebug_Controller_Plugin_Debug_Plugin_Adodb::$execCacheSql[] = $sql;
        
        return null;        
    }    
}