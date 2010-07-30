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
            $updater = new Systemupdater_Model_Onmupdate();
            list($this->command, $this->output, $this->title) = $updater->executeCommand($command, $this->cfg);
        }
    }
    
    
}