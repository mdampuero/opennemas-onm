<?php
class Systemupdater_Model_Onmupdate
{
    
    public $available_scm =  array('svn');
    public $operations = array(
            'co' => array(
                          'cmd' => 'svn co --username scm_username --password  scm_password scm_repository scm_destination',
                          'title' => 'Checking out'),
                    
            'status' => array('cmd' => 'svn status scm_destination',
                              'title' => 'SVN Status'),
            
            'update' => array('cmd' => 'svn update --username scm_username --password  scm_password scm_destination/*',
                              'title' => 'Updating SVN'),
            
            'info'   => array('cmd' => 'svn info  --username scm_username --password  scm_password scm_destination',
                              'title' => 'Getting SVN info'),
            
            'list'   => array('cmd' => 'svn list  --username scm_username --password  scm_password scm_destination -v',
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
        
        $output = array();
        exec($cmd, $output, $return_var);
        
        return array( $this->operations[$action]['cmd'], implode("\n", $output), $this->operations[$action]['title'] );
    }
}