<?php

class WidgetMostSeeingVotedCommentedContent extends Widget_Factory {


    public $template = 'widgets/widget_most_seeing_voted_commented_content.class.tpl';
    public $widgetConfig = array( 'maxElements' => 6 );

    public function __construct() {
        parent::__construct();
        $this->ccm = ContentCategoryManager::get_instance();
    }

    public function render($params = array()) {
        
        $this->widgetConfig = array_merge($this->widgetConfig,$params);
        
        if(!isset($actual_category_id)) { $actual_category_id = 0; }

        //public function getMostViewedContent($content_type, $not_empty = false, $category = 0, $author=0, $days=2, $num=9, $all=false)
        //    public function getMostComentedContent($content_type, $not_empty = false, $category = 0, $days=2, $num=9, $all=false)
        //    public function getMostVotedContent($content_type, $not_empty = false, $category = 0, $author=0, $days=2, $num=8, $all=false)

        // Retrieve all the required contents
        $articlesMostViewed = $this->cm->cache->getMostViewedContent('Article', true, $actual_category_id, 0, 2, $this->widgetConfig['maxElements'] );
        $articlesMostCommented = $this->cm->cache->getMostComentedContent('Article', true, $actual_category_id, 2, $this->widgetConfig['maxElements']);
        $articlesMostVoted = $this->cm->cache->getMostVotedContent('Article', true, $actual_category_id, 0, 2, $this->widgetConfig['maxElements']);
        
        // for most commented articles fetch the article data and fill its category, needed for uri generation
        $articlesMostCommentedImproved = array();
        if (!empty($articlesMostCommented) && count($articlesMostCommented) > 0) {
            foreach ($articlesMostCommented as $art) {
                    $article = new Article($art['pk_content']);
                    $article->category_name = $this->ccm->get_name((int)$article->category);
                    $articlesMostCommentedImproved[] = $article;
            }
        }
        
        // Assign them to the template object
        $this->tpl->assign('articlesMostViewed', $articlesMostViewed);
        $this->tpl->assign('articlesMostVoted', $articlesMostVoted);
        $this->tpl->assign('articlesMostCommented', $articlesMostCommentedImproved);
        $this->tpl->assign('widgetConfig', $this->widgetConfig);


        // return the html output
        return $this->tpl->fetch($this->template);
    }

}
