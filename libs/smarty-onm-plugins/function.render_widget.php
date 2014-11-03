<?php
function smarty_function_render_widget($params, &$smarty)
{
    // Initializing parameters
    $widgetName = isset($params['name']) ? $params['name'] : null;
    $widgetID   = isset($params['id'])   ? $params['id']   : null;

    if (!is_null($widgetName)) {
        // Initialize widget from name
        $widget = new Widget();
        $widget->readIntelligentFromName($widgetName);
    } else {
        // Initialize widget from id
        $er = getService('entity_repository');
        $widget = $er->find('Widget', $widgetID);
    }

    $output = '';
    if ($widget->available) {
        $output = $widget->render($params);
    }

    // Render its contents
    return $output;
}
