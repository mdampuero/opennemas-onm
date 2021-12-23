<?php

use Api\Exception\GetItemException;
use Api\Exception\GetListException;

function smarty_function_render_widget($params, &$smarty)
{
    // Initializing parameters
    $widgetName = isset($params['name']) ? $params['name'] : null;
    $widgetID   = isset($params['id']) ? $params['id'] : null;

    $sw = getService('api.service.widget');

    $oql = 'content_type_name="widget"'
        . ' and content_status = 1'
        . ' and in_litter = 0 ';

    $oql .= !is_null($widgetName)
        ? sprintf(' and class = "%s"', $widgetName)
        : sprintf(' and pk_content = "%s"', $widgetID);

    try {
        $widget = $sw->getItemBy($oql);
    } catch (GetItemException $e) {
        return '';
    }

    $output = '';
    if ($widget->content_status) {
        $output = $smarty->getContainer()
            ->get('frontend.renderer')->render($widget, $params);
    }

    // Render its contents
    return $output;
}
