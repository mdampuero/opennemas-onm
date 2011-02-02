<?php

class WidgetMostSeeingVotedCommentedContent extends Widget_Factory {
    
    
    public $template = 'widgets/widget_most_seeing_voted_commented_content.class.tpl';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function render($params = '') {
        $articles_viewed = $this->cm->cache->getMostViewedContent('Article', true, $actual_category_id, 0, 7, 6);
        $articles_comments = $this->cm->cache->getMostComentedContent('Article', true, $actual_category_id, 0, 7, 6);
        $articles_voted = $this->cm->cache->getMostVotedContent('Article', true, $actual_category_id, 0, 7, 6);
        $arts_commented = array();
        if (!empty($articles_comments) && count($articles_comments) > 0) {
            foreach ($articles_comments as $arts) {
                $this_article = new Article($arts['pk_content']);
                $arts_commented[] = $this_article;
            }
        }
        $this->tpl->assign('articles_viewed', $articles_viewed);
        $this->tpl->assign('articles_voted', $articles_voted);
        $this->tpl->assign('articles_comments', $arts_commented);
        
        return $this->tpl->fetch($this->template);
    }
    
}