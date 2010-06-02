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
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class SvnController extends Onm_Controller_Action
{
    private $cfg = array();
    
    public function init()
    {
        // FIXME: use config file
        $this->cfg = array(
            'username'    => $_SESSION['username'],
            'repository'  => 'http://svn.openhost.es/opennemasdemo/trunk/www',
            'destination' => '/home/opennemas/webdev',
        );
        
        $fields = array('username', 'password', 'repository', 'destination');
        foreach($fields as $key) {
            if(isset($_REQUEST[$key]) && !empty($_REQUEST[$key])) {
                $this->cfg[$key] = $_REQUEST[$key]; 
            }
        }
    }    
    
    /**
     * Route: svn-index
     *  /svn/*
     */
    public function indexAction()
    {
        $command = $this->_getParam('command', null);                
        
        if($command != null) {
            list($command, $output, $title) = $this->processCommand($command);
            
            $this->tpl->assign('command', $command);
            $this->tpl->assign('output',  $output);
            $this->tpl->assign('title',   $title);
        }
        
        $this->tpl->assign('cfg', $this->cfg);        
        $this->tpl->display('svn.tpl');
    }            
    
    private function processCommand($action)
    {
        $operations = array(
            'co' => array('cmd' => 'svn co --username '.$this->cfg['username'] . ' --password ' . $this->cfg['password'] .
                                   ' ' . $this->cfg['repository'] . ' ' . $this->cfg['destination'],
                          'title' => 'Checking out'),
                    
            'status' => array('cmd' => 'svn status ' . $this->cfg['destination'],
                              'title' => 'SVN Status'),
            
            'update' => array('cmd' => 'svn update --username ' . $this->cfg['username'] . ' --password ' . $this->cfg['password'] .
                                       ' ' . $this->cfg['destination'] . '/*',
                              'title' => 'Updating SVN'),
            
            'info'   => array('cmd' => 'svn info  --username ' . $this->cfg['username'] . ' --password ' . $this->cfg['password'] .
                                       ' ' . $this->cfg['repository'],
                              'title' => 'Getting SVN info'),
            
            'list'   => array('cmd' => 'svn list  --username ' . $this->cfg['username'] . ' --password ' . $this->cfg['password'] .
                                       ' ' . $this->cfg['repository'] . ' -v',
                              'title' => 'Listing SVN files'),
            
            'ps'     => array('cmd' => 'ps -el',
                              'title' => 'Process list'),
            
            'netstat'     => array('cmd' => 'netstat -putan',
                              'title' => 'Netstat'),             
        );
        
        if(!isset($operations[$action])) {
            return array('', '', 'Undefined');
        }
        
        $output = array();
        exec($operations[$action]['cmd'], &$output, &$return_var);
        
        return array( $operations[$action]['cmd'], implode("\n", $output), $operations[$action]['title'] );
    }
    
}