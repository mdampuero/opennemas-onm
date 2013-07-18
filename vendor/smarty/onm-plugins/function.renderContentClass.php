<?php
/*
 * -------------------------------------------------------------
 * File:     	function.humandate.php
 */
function smarty_function_renderContentClass($params, &$smarty) {
    if (!isset($params['item'])) {
        $smarty->trigger_error("get_property: missing 'item' parameter");
        return;
    }

    $item = $params['item'];
    if ($params['category'] == 'home') {
        $category = 0;
    } else {
        $category = $params['category'];
    }

    $item = $params['item'];

    $element = 'format_'.$category;
    $properties = $item->getProperty($element);

    return  "type-{$item->content_type} category-{$item->category} $properties";
}
