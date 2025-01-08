<?php

use Api\Exception\GetListException;

/**
 * Used to generate links for tags depending on the selected method
 * twitter hashtag, onm internal tags, google search
 */
function smarty_function_renderMetaKeywords($params, &$smarty)
{
    if (!array_key_exists('content', $params)
        || empty($params['content'])
        || empty($params['content']->tags)
    ) {
        return '';
    }

    $content = $params['content'];
    $ids     = !empty($content->tags) ? $content->tags : [];

    try {
        // Set private flag to false
        $tags = $smarty->getContainer()->get('api.service.tag')
            ->getListByIds($ids, false)['items'];
    } catch (GetListException $e) {
        return '';
    }

    $tags = array_map(function ($tag) {
        return $tag->name;
    }, $tags);

    if (array_key_exists('onlyTags', $params) && $params['onlyTags']) {
        return implode(',', $tags);
    }

    return '<meta name="keywords" content="' . html_attribute(implode(',', $tags)) . '" />';
}
