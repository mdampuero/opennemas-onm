<?php
class PostControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
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
    
    public function testBlogPostIndexAction()
    {
        $this->loginUser('admin', '12admin34');
        $this->request->setMethod('GET');        
        
        $this->dispatch('/blog/post/');        
        
        $this->assertModule('blog');
        $this->assertController('post');
        $this->assertAction('index');
        $this->assertRoute('blog-post-index');
    }
    
    public function testBlogPostShowAction()
    {
        $this->loginUser('admin', '12admin34');
        $this->request->setMethod('GET');
        
        $this->dispatch('/blog/post/show/');        
        
        $this->assertModule('blog');        
        $this->assertController('post');
        $this->assertAction('show');
        $this->assertRoute('blog-post-show');
        $this->assertNotModule('default');
        $this->assertXpath('//h1');
    }    
    
    public function testBlogPostIndexContainsUlList()
    {
        $this->loginUser('admin', '12admin34');
        $this->request->setMethod('GET');        
        
        $this->dispatch('/blog/post/');   
        $this->assertQuery('ul#list li');
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