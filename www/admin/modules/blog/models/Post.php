<?php

class Blog_Model_Post
{
    public function getPosts()
    {
        return array(1,2,3,4);
    }
    
    public function getPost()
    {
        return array('title' => 'Titulín');
    }
}