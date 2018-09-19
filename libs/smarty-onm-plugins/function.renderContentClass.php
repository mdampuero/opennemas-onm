<?php
/**
 * Renders the CSS classes for a given content
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_function_renderContentClass($params, &$smarty)
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

    $item = $params['item'];

    $element    = 'format_' . $category;
    $properties = $item->getMetadata($element);

    return "type-{$item->content_type} category-{$item->category} $properties";
}
