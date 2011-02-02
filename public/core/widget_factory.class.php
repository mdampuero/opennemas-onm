<?php

class Widget_Factory {
    
    public function __construct($use_db = true) {
        
        if($use_db) {
            $this->cm = new ContentManager();
            $this->ccm = ContentCategoryManager::get_instance();
        }
        $this->tpl = new Template(TEMPLATE_USER);
        $this->tpl->caching = 0;
        $this->tpl->force_compile = true;
        
        // Assign a random number, usefull for diferenciate instances of
        // the same widget
        $this->tpl->assign('rnd_number', rand(5, 900));
    }
}