<?php

class WidgetOpinionAuthorList extends Widget_Factory {


    public $template = 'widgets/widget_opinion_author_list.class.tpl';

    public function __construct() {
        parent::__construct();
    }

    public function render($params = '') {

        // Fetch a list of authors to display the dropdown
        $aut = new Author();
        $all_authors = $aut->cache->all_authors(NULL,'ORDER BY name');
        $this->tpl->assign('list_all_authors', $all_authors);

        // return the html output
        return $this->tpl->fetch($this->template);
    }

}
