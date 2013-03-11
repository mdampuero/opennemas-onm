<?php
function smarty_function_getProperty($params, &$smarty)
{


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

    $properties = explode(', ', $params['property']);
    $output = '';
    $end='';

    if (is_array($properties)) {
        if (!empty($params['style'])) {
            $output = " style =\"";
            $end = "\"";
        }
        foreach ($properties as $key => $property) {
            $prop = $property."_".$category;
            $value = $item->getProperty($prop);
            if (!empty($value)) {
                if ($property == 'bgcolor') {
                    $output .= "background-color:{$value};";
                } else {
                    $output .= "{$value};";
                }
            }
        }
    }

    return $output.$end;
}

