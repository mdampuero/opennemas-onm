<?php

class WidgetOnmTwitter extends Widget_Factory {

    public $template = 'widgets/widget_onm_twitter.class.tpl';

    public function __construct() {
        parent::__construct(false);
    }

    public function render($params = '') {

        // Fetch a list of authors to display the dropdown
        $users = "'nuevatribuna'";
        $this->tpl->assign('users', $users);
        
        // return the html output
        return $this->tpl->fetch($this->template, 'twitter-');
    }

}
