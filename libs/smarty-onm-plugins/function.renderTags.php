<?php
/**
 * Used to generate links for tags depending on the selected method
 * twitter hashtag, onm internal tags, google search
 **/

function smarty_function_renderTags($params, &$smarty)
{
    $output = '';
    // If no metadata return empty output
    if (!array_key_exists('metas', $params)) {
        return $output;
    }

    // Check for separator
    $separator = (!array_key_exists('separator', $params)) ? ', ' : $params['separator'];

    // Check for class
    $class = (!array_key_exists('class', $params)) ? ' class="tags" ': $params['class'];

    // Check for limit
    $limit = (array_key_exists('limit', $params)) ? $params['limit'] : null;

    // Get url generator
    $generator = getService('router');

    // Get Google Search Key
    $googleSearchKey = \Onm\Settings::get('google_custom_search_api_key');

    // Check for search method
    if (array_key_exists('internal', $params)) {
        $method = ($params['internal'] === true)? 'tags': $params['internal'];
    } else {
        $method = (!empty($googleSearchKey))? 'google': 'tags';
    }

    // Generate tags links
    foreach ($params['metas'] as $key => $tag) {
        $tag = trim($tag);
        if (!empty($tag)) {
            $result = preg_match('/^#(.*)/', $tag, $matches);
            $url = $target = '';
            switch ($method) {
                case 'hashtag':
                    if (!empty($matches[1])) {
                        $baseUrl = 'https://twitter.com/hashtag/';
                        $url = htmlentities($baseUrl . $matches[1], ENT_QUOTES);
                        $target = 'target="_blank"';
                    }
                    break;

                case 'google':
                    $baseUrl = $generator->generate('frontend_search_google');
                    $url = $baseUrl.'?q='.$tag.'&ie=UTF-8&cx='.$googleSearchKey;
                    break;

                case 'tags':
                    $url = $generator->generate('tag_frontpage', ['tag_name' => $tag]);
                    break;
            }
            if (!empty($url)) {
                $output .= '<a '.$class.' '.$target.' href="'.$url.
                    '" title="'.$tag.'">'.$tag.'</a>'.$separator;
            }
        }
        if (!is_null($limit) && $key == ($limit - 1)) {
                return $output;
        }
    }

    return $output;
}
