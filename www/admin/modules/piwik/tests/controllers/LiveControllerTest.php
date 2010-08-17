<?php
// http://till.klampaeckel.de/blog/archives/21-Avoiding-common-pitfalls-with-Zend_Test.html
class LiveControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public $application = null;
    
    public function setUp()
    {
        Zend_Session::$_unitTestEnabled = true;
        
        // Assign and instantiate in one step:
        $this->application = new Zend_Application(
            'testing', 
            APPLICATION_PATH . '/configs/application.ini'
        );
        
        $this->bootstrap = array($this, 'initSession');
        parent::setUp();        
    }    
    
    
    public function testLiveGetLastVisitsAction()
    {
        $this->dispatch('/piwik/live/lastvisits/');
        
        $this->assertModule('piwik');
        $this->assertController('live');
        $this->assertAction('lastvisits');
        $this->assertResponseCode(200);
        $this->assertXpath('//table[@id="datagrid"]');
    }
    
    public function initSession()
    {
        $this->application->bootstrap();        
    }

}