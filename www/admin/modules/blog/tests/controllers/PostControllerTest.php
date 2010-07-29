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
        $this->dispatch('/blog/post/');        
        
        $this->assertModule('blog');
        $this->assertController('post');
        $this->assertAction('index');
        $this->assertRoute('blog-post-index');
    }
    
    public function testBlogPostIndexContainsUlList()
    {
        $this->dispatch('/blog/post/');   
        $this->assertQuery('ul#list li');
    }
    
    public function initSession()
    {
        $this->application->bootstrap();      
    }

}