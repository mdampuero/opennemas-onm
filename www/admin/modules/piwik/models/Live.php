<?php
class Piwik_Model_Live
{
    public function lastVisits()
    {
        //Live.getLastVisits (idSite, limit = '10', minIdVisit = '')
        $client = new Zend_Rest_Client('https://piwik.openhost.es/admin/index.php?module=API&method=Live.getLastVisits&idSite=10&format=xml&token_auth=6e562cc82d051357d3eba60c0cec76ae&limit=20');
        
        $result = $client->get();        
        
        return $result;
    }
}