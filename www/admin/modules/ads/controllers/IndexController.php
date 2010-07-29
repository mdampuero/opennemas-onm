<?php

class Ads_IndexController extends Onm_Controller_Action
{
    public $zones = null;
    public $banner  = null;
    public $banners = null;
    
    public function indexAction()
    {        
        $client = new Zend_XmlRpc_Client('http://localhost/openx-2.8.5/www/api/v2/xmlrpc');
        $ox = $client->getProxy('ox');
        
        $sess = $ox->logon('admin', 'admin');
        
        //$rs = $ox->getAgencyList($sess);
        
        $agencyId = 2;        
        
        $rs = $ox->getPublisherListByAgencyId($sess, $agencyId);
        
        $publisherId = $rs[0]['publisherId'];
        $zones = $ox->getZoneListByPublisherId($sess, $publisherId);                
        
        foreach($zones as $zone) {
            /*$this->zones[] = $zone;*/
            $this->banners[] = $ox->generateTags($sess,
                                               $zone['zoneId'],
                                               'invocationTags:oxInvocationTags:adjs',
                                               array());
        }
        
        //$rs = $ox->getBannerListByCampaignId($sess, 3);
        //foreach($rs as $banner) {
        //    $b = $ox->getBanner($sess, $banner['bannerId']);
        //    
        //    var_dump($b);
        //}
        
        //$rs = $ox->getPublisherListByAgencyId($sess, 3);
        
        //$rs = $ox->getZoneListByPublisherId($sess, 3);
        
        //$contentTypes = array(
        //    'invocationTags:oxInvocationTags:adjs',
        //    'invocationTags:oxInvocationTags:adlayer',
        //    'invocationTags:oxInvocationTags:adframe',
        //    'invocationTags:oxInvocationTags:adviewnocookies',
        //    'invocationTags:oxInvocationTags:local'
        //);
        //$this->banner = $ox->generateTags($sess, 3, 'invocationTags:oxInvocationTags:adjs', array());
        
        $ox->logoff($sess);        
    }
    
}