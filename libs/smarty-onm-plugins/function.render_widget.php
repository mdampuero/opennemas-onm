<?php
function smarty_function_render_widget($params, &$smarty)
{
    // Initializing parameters
    $widgetName = isset($params['name']) ? $params['name'] : null;
    $widgetID   = isset($params['id']) ? $params['id'] : null;

    $us = getService('api.service.content');

    if (!is_null($widgetName)) {
        // Initialize widget from name
        $sql = sprintf(
            'SELECT contents.* FROM contents INNER JOIN contentmeta'
            . ' ON pk_content = fk_content '
            . ' WHERE meta_name = "class"'
            . ' and meta_value = "%s"'
            . ' and content_type_name = "widget"'
            . ' and content_status = 1'
            . ' and in_litter = 0'
            . ' limit 1',
            $widgetName
        );
    } else {
        $sql = sprintf(
            'SELECT contents.* FROM contents INNER JOIN contentmeta'
            . ' ON pk_content = fk_content '
            . ' WHERE content_type_name = "widget"'
            . ' and content_status = 1 and'
            . ' and in_litter = 0'
            . ' and pk_content = %s',
            $widgetID
        );
    }

    $widget = $us->getListBySql($sql)['items'];

    $output = '';
    if ($widget->content_status) {
        $output = $smarty->getContainer()
            ->get('frontend.renderer')->render($widget, $params);
    }

    // Render its contents
    return $output;
}
