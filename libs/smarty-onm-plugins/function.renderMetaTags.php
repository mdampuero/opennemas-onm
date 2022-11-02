<?php

use Api\Exception\GetListException;

/**
 * Used to generate links for tags depending on the selected method
 * twitter hashtag, onm internal tags, google search
 */
function smarty_function_renderMetaTags($params, &$smarty)
{
    $content = $smarty->getValue('content') ??
        $smarty->getValue('item') ??
        $smarty->getValue('poll') ??
        $smarty->getValue('author') ??
        $smarty->getValue('tag') ??
        $smarty->getValue('category');

    $page = $smarty->getValue('page') ?? null;

    $mh = $smarty->getContainer()->get('core.helper.meta');
    $output = $mh->generateMetas($content, $page);

    return $output;
}
