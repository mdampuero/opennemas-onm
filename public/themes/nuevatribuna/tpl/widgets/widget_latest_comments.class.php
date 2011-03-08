<?php

class WidgetLatestComments extends Widget_Factory {


    public $template = 'widgets/widget_latest_comments.class.tpl';

    public function __construct() {
        
        parent::__construct();
        $this->ccm = ContentCategoryManager::get_instance();

    }

    public function render($params = '') {

        // Retrieve all the required contents
        $articles_comments = $this->cm->cache->getLatestComments();
        $arts_commented = array();
        if (!empty($articles_comments) && count($articles_comments) > 0) {
            foreach ($articles_comments as $arts) {

                $this_article = new Article($arts);
                $this_article->category_name = $this->ccm->get_name((int)$this_article->category);

                $arts_commented[] = $this_article;

            }
        }

        // Assign them to the template object
        $this->tpl->assign('articles_comments', $arts_commented);

        // return the html output
        return $this->tpl->fetch($this->template);
    }

}
