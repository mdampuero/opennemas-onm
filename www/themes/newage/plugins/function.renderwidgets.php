<?php
function smarty_function_renderwidgets($params, &$smarty) {
    $output = '';

    $placeholder = $params['placeholder'];
    $category    = $params['category'];        
    
    // Recuperar los widgets para este placeholder WidgetManager
    $cm = new ContentManager();
    $widgets = $cm->find('Widget', '`fk_content_type`=12 AND `available`=1 AND `placeholder`="'.$placeholder.'"',
              'ORDER BY position ASC, created DESC ');
    
    foreach($widgets as $widget) {
        $output .= $widget->render($smarty);
        //var_dump( $widget->render($smarty) );
    }

    return $output . $category;
}