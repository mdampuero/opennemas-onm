<?php

class Systemupdater_IndexController extends Onm_Controller_Action
{

    public $zones = null;

    public $command = null;
    public $output = null;
    public $title = null;
        
    public $cfg = array();

    public function preDispatch(){

        $request = Zend_Controller_Front::getInstance()->getRequest();
        
        $fields = array('scm_username', 'scm_password', 'scm_repository', 'destination');
        foreach($fields as $key) {
            $param = $request->getParam($key);
            if(isset($param) && !empty($param)) {
                $this->cfg[$key] = $request->getParam($key); 
            }
            
        }

    }
        

    public function init()
    {
        
        // FIXME: use config file
        $this->cfg = array(
            'scm_username'    => Zend_Registry::get('session')->username,
            'scm_repository'  => 'http://svn.openhost.es/opennemasdemo',
            'scm_destination' => SITE_PATH,
         );
        
    }    
    
    /**
     * Route: svn-index
     *  /svn/*
     */
    public function indexAction()
    {
        $command = $this->_getParam('command', null);
        
        if($command != null) {
            list($this->command, $this->output, $this->title) = $this->processCommand($command);
        }
    }            
    
    private function processCommand($action)
    {
        $operations = array(
            'co' => array('cmd' => 'svn co --username '.$this->cfg['scm_username'] . ' --password ' . $this->cfg['scm_password'] .
                                   ' ' . $this->cfg['scm_repository'] . ' ' . $this->cfg['destination'],
                          'title' => 'Checking out'),
                    
            'status' => array('cmd' => 'svn status ' . $this->cfg['destination'],
                              'title' => 'SVN Status'),
            
            'update' => array('cmd' => 'svn update --username ' . $this->cfg['scm_username'] . ' --password ' . $this->cfg['scm_password'] .
                                       ' ' . $this->cfg['destination'] . '/*',
                              'title' => 'Updating SVN'),
            
            'info'   => array('cmd' => 'svn info  --username ' . $this->cfg['scm_username'] . ' --password ' . $this->cfg['scm_password'] .
                                       ' ' . $this->cfg['scm_repository'],
                              'title' => 'Getting SVN info'),
            
            'list'   => array('cmd' => 'svn list  --username ' . $this->cfg['scm_username'] . ' --password ' . $this->cfg['scm_password'] .
                                       ' ' . $this->cfg['scm_repository'] . ' -v',
                              'title' => 'Listing SVN files'),
            
            'ps'  => array('cmd' => 'ps -auxx', 'title' => 'Process information')
            
            /*'ps'     => array('cmd' => 'ps -el',
                              'title' => 'Process list'),
            
            'netstat'     => array('cmd' => 'netstat -putan',
                              'title' => 'Netstat'), */            
        );
        
        if(!isset($operations[$action])) {
            return array('', '', 'Undefined');
        }
        
        $output = array();
        exec($operations[$action]['cmd'], $output, $return_var);
        
        return array( $operations[$action]['cmd'], implode("\n", $output), $operations[$action]['title'] );
    }
    
    
}