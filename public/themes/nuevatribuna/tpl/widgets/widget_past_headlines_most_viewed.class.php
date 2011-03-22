<?php

class WidgetPastHeadlinesMostViewed extends Widget_Factory {

    public $template = 'widgets/widget_past_headlines_most_viewed.class.tpl';

    public function __construct() {
        parent::__construct();
    }

    public function render($params = '') {
		
		global $actual_category_id;
		$this->ccm = new ContentCategoryManager();
		if($actual_category_id != 0) {
			
			$category = $this->ccm->find(" pk_content_category = ".$actual_category_id);
			$actual_category_name = $category[0]->title;
		}
		

        $now = date('Y-m-d H:m:s', time()); //2009-02-28 21:00:13
        if (!isset($actual_category_id) || empty($actual_category_id)) {
            $actual_category_id = 0;
        }
        // Search las 24h, 3days, 1week available articles.
        $articles_24h = $this->cm->getAllMostViewed(true, $actual_category_id, 1, 5);
		foreach ($articles_24h as $article ) {
			$category = $this->ccm->find(" pk_content_category = ".$article->category);
			$article->category_name = (isset($category[0]->name)) ? $category[0]->name : 'opinion';
		}		
        $articles_3day = $this->cm->getAllMostViewed(true, $actual_category_id, 3, 5);
		foreach ($articles_3day as $article ) {
			$category = $this->ccm->find(" pk_content_category = ".$article->category);
			$article->category_name = (isset($category[0]->name)) ? $category[0]->name : 'opinion';
		}
        $articles_1sem = $this->cm->getAllMostViewed(true, $actual_category_id, 7, 5);
		foreach ($articles_1sem as $article ) {
			$category = $this->ccm->find(" pk_content_category = ".$article->category);
			$article->category_name = (isset($category[0]->name)) ? $category[0]->name : 'opinion';
		}		
         
		// Assigning vars to template
		$this->tpl->assign('actual_category_name', $actual_category_name);
        $this->tpl->assign('articles_24h', $articles_24h);
        $this->tpl->assign('articles_3day', $articles_3day);
        $this->tpl->assign('articles_1sem', $articles_1sem);
		
        // return the html output
        return $this->tpl->fetch($this->template);
    }

}


