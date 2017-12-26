<?php
/**
 * Used to generate links for tags depending on the selected method
 * twitter hashtag, onm internal tags, google search
 */
function smarty_function_renderTags($params, &$smarty)
{
    $output = '';
    // If no metadata return empty output
    if (!array_key_exists('metas', $params)) {
        return $output;
    }

    // Check and sanitize params: separator, class, limit
    $separator = (!array_key_exists('separator', $params)) ? ', ' : $params['separator'];
    $class     = (!array_key_exists('class', $params)) ? ' class="tags" ' : $params['class'];
    $limit     = (array_key_exists('limit', $params)) ? $params['limit'] : null;

    // Setup desired rendering method (internal, google search) from internal parameter
    if (array_key_exists('internal', $params)) {
        $method = ($params['internal'] == 'true') ? 'tags' : $params['internal'];
    } else {
        $googleSearchKey = getService('setting_repository')->get('google_custom_search_api_key');
        $method          = (!empty($googleSearchKey)) ? 'google' : 'tags';
    }

    // Get url generator
    $generator = getService('router');

    // Generate tags links
    $i = 0;
    foreach ($params['metas'] as $tag) {
        $tag = trim($tag);
        if (!empty($tag)) {
            $url = $target = '';
            switch ($method) {
                case 'hashtag':
                    if (strpos($tag, '#') === 0) {
                        $baseUrl = 'https://twitter.com/hashtag/';
                        $url     = htmlentities($baseUrl . substr($tag, 1), ENT_QUOTES);
                        $target  = 'target="_blank"';
                    }
                    break;

                case 'google':
                    if (strpos($tag, '#') !== 0) {
                        $baseUrl = $generator->generate('frontend_search_google');
                        $url     = $baseUrl . '?q=' . $tag . '&ie=UTF-8&cx=' . $googleSearchKey;
                    }
                    break;

                case 'tags':
                    if (strpos($tag, '#') !== 0) {
                        $url = $generator->generate('tag_frontpage', [
                            'resource' => 'tags',
                            'tag_name' => $tag
                        ]);
                    }
                    break;
            }

            if (!empty($url)) {
                $url = $smarty->getContainer()->get('core.helper.l10n_route')
                    ->localizeUrl($url, '');

                $output .= '<a ' . $class . ' ' . $target . ' href="' . $url .
                    '" title="' . $tag . '">' . $tag . '</a>' . $separator;
                $i++;
            }
        }

        if (!is_null($limit) && $i === $limit) {
            return $output;
        }
    }

    return $output;
}
