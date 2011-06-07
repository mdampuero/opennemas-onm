<?php

class WidgetLatestCommentsNew extends Widget_Factory {


    public $template = 'widgets/widget_latest_comments_new.class.tpl';

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
                $comments = $this->cm->getLastComentsContent($this_article->content_type, true, $this_article->category, 5);
                foreach ($comments as $comm) {
                    if($this_article->pk_article == $comm['pk_content'] ){
                        $this_article->comment = $comm['comment'];
                        $this_article->pk_comment = $comm['pk_comment'];
                        $this_article->comment_author = $comm['author'];
                        $this_article->comment_title = $comm['comment_title'];
                    }
                }
                
                
                $arts_commented[] = $this_article;
                
            }
        }
        
        // Assign them to the template object
        $this->tpl->assign('articles_comments', $arts_commented);

        // return the html output
        return $this->tpl->fetch($this->template);
    }

}
   