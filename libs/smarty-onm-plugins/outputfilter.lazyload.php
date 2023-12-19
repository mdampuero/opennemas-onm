<?php
/**
 * Add 'loading=lazy' to iframes
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_lazyload($output, $smarty)
{
    $request = $smarty->getContainer()
        ->get('request_stack')
        ->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getUri();

    if (!preg_match('/newsletter/', $smarty->source->resource)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads\//', $uri)
        && !preg_match('/\/admin/', $uri)
        && !preg_match('/\/comments\//', $uri)
        && !preg_match('/\/rss\/(?!listado$)/', $uri)
        && !preg_match('@\.amp\.html@', $uri)
    ) {
        // Check for content, parse and replace youtube embed
        $content = $smarty->getValue('o_content');
        if (!empty($content)) {
            preg_match_all(
                '@<iframe.*?src="https:.*?youtube.com/embed/([^#\&\?\s\"]*).*?".*?>(?s).*?<\/iframe>@',
                $content->body,
                $matches
            );

            if (empty($matches[0])) {
                return $output;
            }

            $codes = [];
            $tpl   = '<div class="lazyframe"'
                . ' data-vendor="youtube"'
                . ' data-title=""'
                . ' data-thumbnail="%s"'
                . ' data-src="%s"'
                . ' data-ratio="16:9">'
                . '</div>';

            $ids = $matches[1];
            foreach ($ids as $id) {
                $codes[] = sprintf(
                    $tpl,
                    'https://i.ytimg.com/vi/' . $id . '/hqdefault.jpg',
                    'https://www.youtube.com/watch?v=' . $id
                );
            }

            $output = str_replace($matches[0], $codes, $output);
        }

        // Add 'loading=lazy' to iframes before returning output
        $output = preg_replace(
            '/<iframe([^<>]*)>/',
            '<iframe loading="lazy" $1>',
            $output
        );
    }

    return $output;
}
