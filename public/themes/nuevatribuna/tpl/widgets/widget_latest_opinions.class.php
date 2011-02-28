<?php

class WidgetLatestOpinions extends Widget_Factory {


    public $template = 'widgets/widget_latest_opinions.class.tpl';

    public function __construct() {
        parent::__construct();
    }

    public function render($params = '') {

        // Retrieve all the required contents
        $latestOpinions = Opinion::getLatestAvailableOpinions();

        // Assign them to the template object
        $this->tpl->assign('latestOpinions', $latestOpinions);

        // return the html output
        return $this->tpl->fetch($this->template);
    }

}
