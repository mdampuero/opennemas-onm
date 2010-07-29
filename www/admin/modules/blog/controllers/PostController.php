<?php

class Blog_PostController extends Onm_Controller_Action
{
    public $message = null;
    public $posts = null;
    
    public function indexAction()
    {
        $this->message = 'Ola mundo';
        
        $post = new Blog_Model_Post();
        $this->posts = $post->getPosts();
    }
    
}