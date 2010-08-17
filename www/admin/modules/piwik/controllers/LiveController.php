<?php

class Piwik_LiveController extends Onm_Controller_Action
{
    public $visits = null;
    
    public function lastvisitsAction()
    {
        // Live.getUsersInLastXMin (idSite, minutes = '30') 
        $live = new Piwik_Model_Live;
        
        $this->visits = $live->lastVisits();
    }
    
    
}