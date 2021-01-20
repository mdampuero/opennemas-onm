<?php

use Api\Exception\GetListException;

/**
 * Used to generate links for tags depending on the selected method
 * twitter hashtag, onm internal tags, google search
 */
function smarty_function_renderTags($params, &$smarty)
{
    if (!array_key_exists('content', $params)
        || empty($params['content'])
        || empty($params['content']->tags)
    ) {
        return '';
    }

    $content   = $params['content'];
    $ids       = !empty($content->tags) ? $content->tags : [];
    $separator = !array_key_exists('separator', $params) ? ', ' : $params['separator'];
    $output    = '';

    try {
        $tags = $smarty->getContainer()->get('api.service.tag')->getListByIds($ids)['items'];

        if (array_key_exists('limit', $params)) {
            $tags = array_slice($tags, 0, $params['limit']);
        }
    } catch (GetListException $e) {
        return '';
    }

    // Generate tags links
    foreach ($tags as $tag) {
        $url = $smarty->getContainer()->get('router')->generate('frontend_tag_frontpage', [
            'slug' => $tag->slug
        ]);

        $url = $smarty->getContainer()->get('core.helper.l10n_route')
            ->localizeUrl($url, '');

        $output .= sprintf(
            '<a href="%s" class="tag-item">%s</a>%s',
            $url,
            $tag->name,
            $separator
        );
    }

    return $output;
}
