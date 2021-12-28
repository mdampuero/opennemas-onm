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
    $ids           = !empty($content->tags) ? $content->tags : [];
    $separator     = !array_key_exists('separator', $params) ? ', ' : $params['separator'];
    $output        = '';

    try {
        $locale = $smarty->getContainer()->get('core.instance')->hasMultilanguage()
            && !in_array($content->content_type_name, $exclude_types)
            ? $smarty->getContainer()->get('core.locale')->getRequestLocale()
            : null;

        $tags = $smarty->getContainer()->get('api.service.tag')->getListByIds($ids)['items'];

        if (array_key_exists('limit', $params)) {
            $count = 0;

            $tags = array_filter($tags, function ($tag) use (&$count, $params, $locale) {
                if ($count < $params['limit'] &&
                (empty($locale) || empty($tag->locale) || $tag->locale == $locale)) {
                        $count++;
                        return $tag;
                }
            });
        }
    } catch (GetListException $e) {
        return '';
    }

    // Generate tags links
    foreach ($tags as $tag) {
        if (empty($locale) || empty($tag->locale) || $tag->locale == $locale) {
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
    }

    return $output;
}
