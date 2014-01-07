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

    global $generator;
    if (array_key_exists('internal', $params) && ($params['internal'] == true)) {
        if (is_object($generator)) {
            $name ='tag_frontpage';
            $url = $generator->generate($name);
        } else {
            $url = '/tag';
        }


    } else {
        $params['internal'] = false;
        if (!array_key_exists('url', $params)) {
            $url ='';
            $googleKey = \Onm\Settings::get('google_custom_search_api_key');

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
    }

    if (!array_key_exists('class', $params)) {
        $class = ' class="tags" ';
    } else {
        //
        $class = $params['class'];
    }

    $i = 1;
    foreach ($params['metas'] as $tag) {
        $tag = trim($tag);
        if (!empty($tag)) {
            if ($params['internal']) {
                $tag2 = \Onm\StringUtils::generateSlug($tag);
                $fullUrl = htmlentities($url.'/'.$tag2, ENT_QUOTES);
            } else {
                $fullUrl = htmlentities($url.'&q='.$tag, ENT_QUOTES);
            }
            $output .= ' <a '.$class.' href="'.$fullUrl.'" title="'. $tag . '">' . $tag . '</a>'. $separator;

            if (array_key_exists('limit', $params) && $params['limit'] <= $i) {
                return $output;
            }
            $i++;
        }
    }

    return $output;
}
