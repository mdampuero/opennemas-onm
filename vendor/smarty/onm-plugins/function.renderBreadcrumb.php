<?php
/**
 * -------------------------------------------------------------
 * File:        function.Breadcrumb.php
 * Check type of menu element and prepare link
 *
 **/

function smarty_function_renderBreadcrumb($params, &$smarty)
{
    $output = '';
    if (!array_key_exists('item', $params)) {
        return $output;
    }

    if (!array_key_exists('separator', $params)) {
        $separator = '/';
    } else {
        $separator = $params['separator'];
    }

    $actualCategory = $params['item']->category;
    $actualCategoryName = $params['item']->category_name;
    $actualCategoryTitle = $params['item']->category_title;

    $ccm = \ContentCategoryManager::get_instance();

    $first = $ccm->categories[$actualCategory];

    $output .= '<a href="/seccion/' . $first->name . '" title="'
        . $first->title . '">' . $first->title . '</a>';

    // If is a synchronized category
    if (substr($_SERVER['REQUEST_URI'], 1, 3) == 'ext') {
        $output = '<a href="/extseccion/' . $actualCategoryName . '" title="'
            . $actualCategoryTitle . '">' . $actualCategoryTitle . '</a>';
    } elseif (!empty($first->fk_content_category)) {
        $second = $ccm->categories[$first->fk_content_category];
        $output = ' <a href="/seccion/' . $second->name
            .'" title="'. $second->title . '">' . $second->title . '</a>'
            . $separator. $output;
    }

    return $output;
}
