<?php

class ContentBox
{
    private $_html    = null;
    private $_content = null;
    private $_params  = array();
    
    /**
     * @param Content $content
     */
    public function __construct($content, $params=null)
    {
        $this->setContent($content);
        
        if(is_array($params)) {
            $this->setParams($params);
        }
    }
    
    public function setContent($content)
    {
        $this->_content = $content;
        
        return $this;
    }
    
    public function setParams($params)
    {
        $this->_params = $params;
    }
    
    public function render($args=array())
    {        
        $mask = new Mask();
        $mask->setContent($this->_content);
        
        $args = array_merge($this->_params, $args);
        
        $this->_html = $mask->apply($args);
        
        return $this->_html;
    }
}