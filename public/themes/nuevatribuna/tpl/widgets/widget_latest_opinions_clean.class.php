<?php

class WidgetLatestOpinionsClean extends Widget_Factory {


    public $template = 'widgets/widget_latest_opinions_clean.class.tpl';

    public function __construct() {
        parent::__construct();
    }

    public function render($params = '') {

        // Retrieve all the required contents
        $latestOpinionsClean = Opinion::getLatestAvailableOpinions();

        // Assign them to the template object
        $this->tpl->assign('latestOpinions', $latestOpinionsClean);

        // return the html output
        return $this->tpl->fetch($this->template);
    }

}
