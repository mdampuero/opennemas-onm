<?php
/**
 * Used to generate links for tags depending on the selected method
 * twitter hashtag, onm internal tags, google search
 */
function smarty_function_renderMetaKeywords($params, &$smarty)
{
    if (!array_key_exists('content', $params)
        || empty($params['content'])
        || empty($params['content']->tags)
        || !array_key_exists('tags', $params)
        || empty($params['tags'])
    ) {
        return '';
    }

    $content = $params['content'];
    $tags    = $params['tags'];
    $ids     = !empty($content->tags)
        ? $content->tags
        : (!empty($content->tags) ? $content->tags : []);

    $finalTags = array_map(function ($a) use ($tags) {
        return array_key_exists($a, $tags) ? $tags[$a]['name'] : null;
    }, $ids);

    $finalTags = array_filter($finalTags, function ($a) {
        return !empty($a);
    });

    if (empty($finalTags)) {
        return '';
    }

    if (array_key_exists('onlyTags', $params) && $params['onlyTags']) {
        return implode(',', $finalTags);
    }

    return '<meta name="keywords" content="' . implode(',', $finalTags) . '" />';
}
