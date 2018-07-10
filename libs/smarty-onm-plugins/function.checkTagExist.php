<?php
/**
 * Used to generate links for tags depending on the selected method
 * twitter hashtag, onm internal tags, google search
 */
function smarty_function_checkTagExist($params, &$smarty)
{
    // If no metadata return empty output
    if (!array_key_exists('content', $params) ||
        !array_key_exists('tags', $params) ||
        !array_key_exists('tag', $params) ||
        !array_key_exists('assign', $params)
    ) {
        return '';
    }

    $content = $params['content'];
    $tags    = $params['tags'];
    $tag     = $params['tag'];
    $assign  = $params['assign'];

    if (empty($content->tag_ids) || !is_array($content->tag_ids)
        || empty($tags) || !is_array($tags) || empty($tag) || empty($assign)
    ) {
        return '';
    }

    foreach ($content->tag_ids as $tagId) {
        if (array_key_exists($tagId, $tags) && $tags[$tagId]['name'] == $tag) {
            $smarty->assign($assign, true);
            return '';
        }
    }
}
