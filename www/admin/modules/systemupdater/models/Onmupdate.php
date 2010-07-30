<?php
class Systemupdater_Model_Onmupdate
{
    
    public $available_scm =  array('svn');
    public $operations = array(
            'co' => array(
                          'cmd' => 'svn co --non-interactive --username scm_username --password  scm_password scm_repository scm_destination 2>&1',
                          'title' => 'Checking out'),
                    
            'status' => array('cmd' => 'svn status scm_destination 2>&1',
                              'title' => 'SVN Status'),
            
            'update' => array('cmd' => 'svn update --non-interactive --username scm_username --password  scm_password scm_destination/* 2>&1',
                              'title' => 'Updating SVN'),
            
            'info'   => array('cmd' => 'svn info --non-interactive --username scm_username --password  scm_password scm_destination 2>&1',
                              'title' => 'Getting SVN info'),
            
            'list'   => array('cmd' => 'svn list --non-interactive --username scm_username --password  scm_password scm_destination -v 2>&1',
                              'title' => 'Listing SVN files'),
            
            'ps'  => array('cmd' => 'ps -auxx', 'title' => 'Process information')
            
            /*'ps'     => array('cmd' => 'ps -el',
                              'title' => 'Process list'),
            
            'netstat'     => array('cmd' => 'netstat -putan',
                              'title' => 'Netstat'), */            
    );
    
    public function __construct(){
        
    }

    
    public function executeCommand($action, $args = null ) {
        
        if(!isset($this->operations[$action]))
        {
            return array('', '', 'Command undefined');
        }

        if (!is_null($args))
        {
            $cmd = strtr($this->operations[$action]['cmd'], $args);
        }else{
            $cmd = $this->operations[$action]['cmd'];
        }
        
        //return array($cmd, $cmd, $cmd);
        
        $output = array();
        exec($cmd, $output, $return_var);
        //var_dump($return_var);
        //die();
        return array( $this->operations[$action]['cmd'], implode("\n", $output), $this->operations[$action]['title'] );
    }
}