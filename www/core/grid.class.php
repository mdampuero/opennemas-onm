<?php


class Grid
{
    private $_filename = null;
    private $_grid = null;
    private $_positions = null;
    
    static private $_instance = null;
    
    private function __construct($filename, $options=array())
    {
        if(!file_exists($filename)) {
            throw new Exception("Grid file not found: " . $filename);
        }
        
        $this->_filename = $filename;
        $this->load();
        
    }
    
    // FIXME: revisar os parámetros de entrada
    public function getInstance($filename, $options=array())
    {
        if(self::$_instance == null) {
            self::$_instance = new Grid($filename, $options=array());
        }
        
        return self::$_instance;
    }
    
    private function load()
    {
        $xml = simplexml_load_file($this->_filename);
        
        $this->_grid = (string)$xml->content;
        $this->_positions = array();
        
        foreach($xml->positions->children() as $position) {
            $this->_positions[] = (string)$position;
        }
        
    }
    
    
    public function __get($name)
    {
        $propertyName = '_' . $name;
        if(property_exists($this, $propertyName)) {
            return $this->{$propertyName};
        }
    }
    
    public function __call($name, $args=array())
    {
        if(preg_match('/^get/', $name)) {
            $propertyName = '_' . strtolower(str_replace('get', '', $name));
            
            return $this->{$propertyName};
        }
    }
    
    /**
     *
     * array('content-left' => array($contentBox1, $contentBox2))
     */
    public function render($contents)
    {
        $html = $this->_grid;
        
        foreach($contents as $placeholder => $items) {
            $output = '';
            foreach($items as $content) {
                $output .= $content->render();
            }
            
            
            $html = str_replace('<!-- #' . $placeholder . '# -->', $output, $html);
        }
        
        return $html;
    }
    
    
}