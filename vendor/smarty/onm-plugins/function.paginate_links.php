<?php
/*
 * -------------------------------------------------------------
 * File:        function.meta_webmaster_google.php
 */
function smarty_function_paginate_links($params, &$smarty) {

    global $sc;
    $generator = $sc->get('url_generator');

    $total = $params['total'];
    $itemsPerPage = $params['items_per_page'];
    $urlName = $params['url'];
    $filters = $params['filters'];

    $paginator = \Onm\Pager\Slider::create(
        $total,
        $itemsPerPage,
        $generator->generate($urlName, $filters)
    );

    return $paginator->links;
}
