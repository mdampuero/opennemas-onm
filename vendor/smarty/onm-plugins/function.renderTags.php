<?php
/**
 * -------------------------------------------------------------
 * File:        function.Breadcrumb.php
 * Check type of menu element and prepare link
 *
 **/

function smarty_function_renderTags($params, &$smarty)
{
    $output = '';
    if (!array_key_exists('metas', $params)) {
        return $output;
    }

    if (!array_key_exists('separator', $params)) {
        $separator = ', ';
    } else {
        $separator = $params['separator'];
    }

    if (!array_key_exists('url', $params)) {
        $url ='';
        $googleKey = \Onm\Settings::get('google_custom_search_api_key');
        global $generator;
        if (is_object($generator)) {
            $name = 'frontend_search_google';
            $url = $generator->generate($name);
            $url = "{$url}?cx=&ie=UTF-8&key={$googleKey}";
        } else {
            $url = "/search/google?cx=&ie=UTF-8&key={$googleKey}";
        }
    } else {
        //
        $url = $params['url'];
    }

    if (!array_key_exists('class', $params)) {
        $class = ' class="tags" ';
    } else {
        //
        $class = $params['class'];
    }

    foreach ($params['metas'] as $tag) {
        $tag = trim($tag);
        if (!empty($tag)) {
            $output .= ' <a '.$class.' href="' . $url.'&q='.$tag
                .'" title="'. $tag . '">' . $tag . '</a>'. $separator;
        }
    }

    return $output;
}
