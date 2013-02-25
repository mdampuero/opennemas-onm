<?php
/**
 * -------------------------------------------------------------
 * File:        function.Breadcrumb.php
 * Check type of menu element and prepare link
 *
 **/

function smarty_function_renderBreadcrumb($params,&$smarty)
{
    $output = '';
    if (!array_key_exists('item', $params)) {
        return $output;
    }


    $actualCategory = $params['item']->category;

    $ccm = \ContentCategoryManager::get_instance();

    $first = $ccm->categories[$actualCategory];

    $output .= '<a href="/section/' . $first->name . '" title="'
        . $first->title . '">' . $first->title . '</a>';

    if (!empty($first->fk_content_category)) {
        $second = $ccm->categories[$first->fk_content_category];
        $output .= ' | <a href="/section/' . $second->name
            .'" title="'. $second->title . '">' . $second->title . '</a>';
    }

    return $output;
}