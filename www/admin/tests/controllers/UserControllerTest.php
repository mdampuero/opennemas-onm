<?php
class UserControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public $application = null;
    
    public function setUp()
    {
        // Assign and instantiate in one step:
        $this->application = new Zend_Application(
            'testing', 
            APPLICATION_PATH . '/configs/application.ini'
        );
        
        $this->bootstrap = array($this, 'initSession');
        parent::setUp();        
    }
    
    public function testDefaultUserLoginAction()
    {
        $this->dispatch('/user/login/');        
        
        $this->assertModule('default');
        $this->assertController('user');
        $this->assertAction('login');
        $this->assertXpath('//form//input[@name="login"]');
        $this->assertRoute('user-login');
    }
    
    public function testLoginSuccess()
    {
        $this->loginUser('admin', '12admin34');
        $this->request->setMethod('GET');
        
        $this->dispatch('/');
        $this->assertNotRedirect();        
        $this->assertRoute('panel-index');
    }
    
    public function testDefaultUserLogoutAction()
    {
        $this->dispatch('/user/logout/');        
        
        $this->assertModule('default');
        $this->assertController('user');
        $this->assertAction('logout');
        $this->assertRoute('user-logout');
        $this->assertRedirectTo('/user/login');        
    }
    
    public function loginUser($user, $pass)
    {
        $this->request->setMethod('POST')
                      ->setPost(array(
                          'login' => $user,
                          'password' => $pass,
                      ));
        $this->dispatch('/user/login');
        $this->assertRedirectTo('/');
        
        $this->resetRequest()
             ->resetResponse();
        $this->request->setPost(array());
    }
    
    public function initSession()
    {
        $this->application->bootstrap();      
    }

}