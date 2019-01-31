<?php
/**
 * Used to generate links for tags depending on the selected method
 * twitter hashtag, onm internal tags, google search
 */
function smarty_function_renderTags($params, &$smarty)
{
    if (!array_key_exists('content', $params)
        || empty($params['content'])
        || (empty($params['content']->tags)
            && empty($params['content']->tag_ids))
        || !array_key_exists('tags', $params)
        || empty($params['tags'])
    ) {
        return '';
    }

    $content = $params['content'];
    $tags    = $params['tags'];
    $ids     = !empty($content->tags)
        ? $content->tags
        : (!empty($content->tag_ids) ? $content->tag_ids : []);

    // Check and sanitize params: separator, class, limit
    $separator = !array_key_exists('separator', $params) ? ', ' : $params['separator'];
    $limit     = array_key_exists('limit', $params) ? $params['limit'] : null;
    $method    = array_key_exists('method', $params) ? $params['method'] : 'tag';
    $key       = '';

    if ($method === 'tag' && $content->fk_content_type != 1) {
        $key = $smarty->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('google_custom_search_api_key');

        if (!empty($key)) {
            $method = 'google';
        }
    }

    $generator = $smarty->getContainer()->get('router');
    $output    = '';

    // Generate tags links
    $i = 0;
    foreach ($ids as $tagId) {
        if (!array_key_exists($tagId, $tags)) {
            continue;
        }

        $url = $generator->generate('frontend_tag_frontpage', [
            'slug' => $tags[$tagId]['slug']
        ]);

        if ($method === 'google') {
            $url = $generator->generate('frontend_search_google', [
                'q'  => $tags[$tagId]['name'],
                'cx' => $key,
                'ie' => 'UTF-8'
            ]);
        }

        $url = $smarty->getContainer()->get('core.helper.l10n_route')
            ->localizeUrl($url, '');

        $output .= sprintf(
            '<a href="%s" title="%s">%s</a>%s',
            $url,
            $tags[$tagId]['name'],
            $tags[$tagId]['name'],
            $separator
        );

        $i++;

        if (!empty($limit) && $i === $limit) {
            return $output;
        }
    }

    return $output;
}
