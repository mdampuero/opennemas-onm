<?php
/*
 * -------------------------------------------------------------
 * File:     	function.render_widget.php
 * Comprueba el tipo y escribe el nombre o la imag
 */
function smarty_function_render_widget($params, &$smarty) {
    
	// Initialicing parameters
	$widgetName = (isset($params['name']) ? $params['name'] : null);
	$widgetID = (isset($params['id']) ? $params['id'] : null);
    $output = '';

	// Initialize database access
	$cm = new ContentManager();
	$ccm = ContentCategoryManager::get_instance();
	
	if(!is_null($widgetName)) {
		// Initialize widget from db
		$widget = new Widget();
		$widget->readIntelligentFromName($widgetName);
	} else {
	
		// Initialize widget from db
		$widget = new Widget();
		$widget->read($widgetID);
	}
	
	if($widget->available) {
		$output = $widget->render($params);
	}
	
	// Render its contents
	return $output;
}