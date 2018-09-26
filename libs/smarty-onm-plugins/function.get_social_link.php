<?php
function smarty_function_get_social_link($params, &$smarty)
{
    if (!array_key_exists('page', $params)
        || empty($params['page'])
        || !array_key_exists('img', $params)
        || empty($params['img'])
    ) {
        return '';
    }

    $output = '<li>'
        . '<a href="%s" target="_blank" title="%s">'
        . '<img src="%s" alt="" />'
        . '</a>'
        . '</li>';

    $url = $smarty->getContainer()->get('orm.manager')
        ->getDataSet('Settings')
        ->get($params['page'] . '_page');

    if (empty($url)) {
        return '';
    }

    return sprintf($output, $url, $params['title'], $params['img']);
}
