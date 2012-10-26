<?php
function smarty_function_getProperty($params, &$smarty)
{


    if (!isset($params['item'])) {
        $smarty->trigger_error("get_property: missing 'item' parameter");
        return;
    }

    $item = $params['item'];
    $category = $params['category'];
    $property = $params['property']."_".$category;

    $value = $item->getProperty($property);
    if (empty($value)) {
        return '';
    }

    if ($params['property'] == 'bgcolor') {
        return " style = \"background-color:{$value}\"";
    }

    return $value;
}

