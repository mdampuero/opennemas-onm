<?php
function smarty_function_render_widget($params, &$smarty)
{
    // Initializing parameters
    $widgetName = isset($params['name']) ? $params['name'] : null;
    $widgetID   = isset($params['id'])   ? $params['id']   : null;

    $er = getService('entity_repository');

    if (!is_null($widgetName)) {
        // Initialize widget from name
        $criteria = [
            'tables'            => [ 'widgets' ],
            'pk_content'        => [
                [ 'value' => 'pk_widget', 'field' => true ]
            ],
            'content'           => [ [ 'value' => $widgetName ] ],
            'content_type_name' => [ [ 'value' => 'widget' ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 0 ] ],
        ];
        $widget = $er->findOneBy($criteria, null, 1, 1);
    } else {
        // Initialize widget from id
        $widget = $er->find('Widget', $widgetID);
    }

    $output = '';
    if ($widget->available) {
        $output = $widget->render($params);
    }

    // Render its contents
    return $output;
}
