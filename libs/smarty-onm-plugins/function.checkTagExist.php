<?php
/**
 * Used to generate links for tags depending on the selected method
 * twitter hashtag, onm internal tags, google search
 *
 * @param array $params The list of parameters passed to the block.
 * @param \Smarty $smarty The instance of smarty.
 *
 * @return null|string
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

    if (empty($tags)) {
        $tags = $smarty->getContainer()
            ->get('api.service.tag')->getListByIdsKeyMapped($content->tags)['items'];
    }

    if (empty($content->tags) || !is_array($content->tags)
        || empty($tags) || !is_array($tags) || empty($tag) || empty($assign)
    ) {
        return '';
    }

    foreach ($content->tags as $tagId) {
        if (array_key_exists($tagId, $tags) && $tags[$tagId]['name'] == $tag) {
            $smarty->assign($assign, true);
            return '';
        }
    }
}
