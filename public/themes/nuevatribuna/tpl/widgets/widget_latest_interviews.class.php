<?php

class WidgetLatestInterviews extends Widget_Factory {


    public $template = 'widgets/widget_latest_interviews.class.tpl';

    public function __construct() {
        parent::__construct();
    }

    public function render($params = array()) {

        // Retrieve all the required contents
        $contents = array();

		// Setting up default parameters
		$default_params = array(
			'limit' => 6,
		);
		$options = array_merge($default_params, $params);
		$_sql_limit = " LIMIT 0, ".$options['limit']." ";
        $this->tpl->assign('maxInterviews',$options['limit']);
        

        $cm = new ContentManager();
		$ccm = ContentCategoryManager::get_instance();
        
		// get Latest interviews excluding already present in this frontpage
        $latestInterviews = 
            $cm->find_by_category_name('Article',
                                        'entrevistas'
                                        , 'content_status=1 AND frontpage=1'
                                        . ' AND available=1 AND fk_content_type=1'
                                        . ' AND (starttime="0000-00-00 00:00:00" '
                                        . '      OR (starttime != "0000-00-00 00:00:00" '
                                        . '      AND starttime<"'.$now.'"))'
                                        . ' AND (endtime="0000-00-00 00:00:00" OR (endtime != "0000-00-00 00:00:00"  AND endtime>"'.$now.'"))'
                                        , 'ORDER BY position ASC LIMIT 0 , 6');
            
        // Get the category_name for all the interviews
        foreach($latestInterviews as $interview){
            $interview->category_name = 'entrevistas';
        }
        
        // Assign them to the template object
        $this->tpl->assign('latestInterviews', $latestInterviews);

        // return the html output
        return $this->tpl->fetch($this->template);
    }

}
