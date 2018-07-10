<?php
/**
 * Used to generate links for tags depending on the selected method
 * twitter hashtag, onm internal tags, google search
 */
function smarty_function_renderMetaKeywords($params, &$smarty)
{
    // If no metadata return empty output
    if (!array_key_exists('content', $params) || !array_key_exists('tags', $params)) {
        return '';
    }

    $content = $params['content'];
    $tags    = $params['tags'];

    if (empty($content->tag_ids) || !is_array($content->tag_ids)
        || empty($tags) || !is_array($tags)
    ) {
        return '';
    }

    $finalTags = [];
    foreach ($content->tag_ids as $tagId) {
        if (array_key_exists($tagId, $tags)) {
            $finalTags[] = $tags[$tagId]['name'];
        }
    }

    if (empty($finalTags)) {
        return '';
    }

    if (array_key_exists('onlyTags', $params) && $params['onlyTags']) {
        return implode(',', $finalTags);
    }

    return '<meta name="keywords" content="' . implode(',', $finalTags) . '" />';
}
