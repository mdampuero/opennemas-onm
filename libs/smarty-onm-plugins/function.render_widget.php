<?php
function smarty_function_render_widget($params, &$smarty)
{
    // Initializing parameters
    $widgetName = isset($params['name']) ? $params['name'] : null;
    $widgetID   = isset($params['id']) ? $params['id'] : null;

    $sw = getService('api.service.widget');

    if (!is_null($widgetName)) {
        // Initialize widget from name
        $oql = sprintf(
            'content_type_name="widget" and content_status = 1'
            . ' and in_litter = 0 '
            . ' and class = "%s"'
            . ' limit 1',
            $widgetName
        );

        $widget = $sw->getList($oql)['items'][0];
    } else {
        // Initialize widget from id
        $widget = $sw->getItem($widgetID);
    }

    $output = '';
    if ($widget->content_status) {
        $output = $smarty->getContainer()
            ->get('frontend.renderer')->render($widget, $params);
    }

    // Render its contents
    return $output;
}
