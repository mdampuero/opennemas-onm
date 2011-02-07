<?php
/*
 * -------------------------------------------------------------
 * File:     	function.render_widget.php
 * Comprueba el tipo y escribe el nombre o la imag
 */
function smarty_function_render_widget($params, &$smarty) {
    
	// Initialicing parameters
	$widgetID = $params['id'];
    $output = '';

	// Initialize database access
	$cm = new ContentManager();
	$ccm = ContentCategoryManager::get_instance();
    
	// Initialize widget from db
	$widget = new Widget();
	$widget->read($widgetID);
    
	if($widget->available) {
		$output = $widget->render();
	}
	
	// Render its contents
	return $output;
}