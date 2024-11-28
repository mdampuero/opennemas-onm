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

    $exclude_types = [ 'letter' ];
    $content       = $params['content'];

    if (empty($content->tags)) {
        return '';
    }

    $ids       = $content->tags;
    $separator = !array_key_exists('separator', $params) ? ', ' : $params['separator'];
    $output    = '';

    try {
        $multilanguage = $smarty->getContainer()->get('core.instance')->hasMultilanguage();

        $locale = $multilanguage
            && !in_array($content->content_type_name, $exclude_types)
            ? $smarty->getContainer()->get('core.locale')->getRequestLocale()
            : null;

        $oql = sprintf(
            'id in [%s] and (novisible != 1 or novisible is null or novisible = "")',
            implode(',', $ids)
        );

        if ($multilanguage) {
            $oql .= sprintf(
                ' and (locale is null or locale = \'%s\')',
                $locale
            );
        }

        if (array_key_exists('limit', $params) && !empty($params['limit'])) {
            $oql .= sprintf(
                ' limit %s',
                $params['limit']
            );
        }

        $tags = $smarty->getContainer()->get('api.service.tag')->getList($oql)['items'];
    } catch (GetListException $e) {
        return '';
    }

    // Generate tags links
    foreach ($tags as $tag) {
        $url = $smarty->getContainer()->get('router')->generate('frontend_tag_frontpage', [
            'slug' => $tag->slug
        ]);

        $url = $smarty->getContainer()->get('core.decorator.url')->prefixUrl($url);

        $output .= sprintf(
            '<a href="%s" class="tag-item">%s</a>%s',
            $url,
            $tag->name,
            $separator
        );
    }

    return $output;
}
