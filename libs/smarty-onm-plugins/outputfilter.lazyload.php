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
        // Check for content, parse and replace embed
        $content = $smarty->getValue('o_content');
        if (!empty($content)) {
            preg_match_all(
                '@<iframe.*?src="(https:.*?'
                . '(vimeo.com/video/|youtube.com/embed/|dailymotion.com/embed/video/)'
                . '([^#\&\?]*)).*?".*?>(?s).*?<\/iframe>@',
                $content->body,
                $matches
            );

            $urls = str_replace(
                ['youtube.com/embed/', 'player.vimeo.com/video/', 'dailymotion.com/embed/'],
                ['youtube.com/watch?v=', 'vimeo.com/', 'dailymotion.com/'],
                $matches[1]
            );

            $codes = [];
            $tpl   = '<div class="lazyframe"'
                . ' data-vendor="%s"'
                . ' data-title="%s"'
                . ' data-thumbnail="%s"'
                . ' data-src="%s"'
                . ' data-ratio="16:9">'
                . '</div>';

            $cache = $smarty->getContainer()
                ->get('cache.connection.instance');

            foreach ($urls as $key => $url) {
                $video = $cache->get(current(explode('.', $matches[2][$key])) . '_' . $matches[3][$key]);

                if (empty($video)) {
                    $video = unserialize(
                        $smarty->getContainer()
                            ->get('data.manager.filter')
                            ->set($url)
                            ->filter('panorama')
                            ->get()
                    );

                    $cache->set(current(explode('.', $matches[2][$key])) . '_' . $matches[3][$key], $video, 3600 * 24);
                }

                if (empty($video)) {
                    unset($matches[0][$key]);
                    continue;
                }

                $codes[] = sprintf(
                    $tpl,
                    $video['service'] === 'Dailymotion' ? '' : strtolower($video['service']),
                    htmlspecialchars($video['title']),
                    $video['thumbnail'],
                    $video['embedUrl']
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
