<?php
function smarty_function_render_video($params, &$smarty)
{
    $video = $params['video'];

    $output = $video->author_name == 'script'
        ? '<div class="video-container">' . $video->body . '</div>'
        : getVideoOutput($params, $video);

    if ($params['amp']) {
        $output = getService('data.manager.filter')
            ->set($output)
            ->filter('amp')
            ->get();
    }

    return $output;
}

function getVideoOutput($params, $video)
{
    $videoInfo = is_array($video->information)
        ? $video->information
        : unserialize($video->information);

    $width  = $params['width'] ?? '560';
    $height = $params['height'] ?? '320';

    // Videos mp4, ogg, webm and flv
    if (!empty($videoInfo['source'])) {
        $output = '<video controls width="' . $width . '" height="' . $height . '">';
        foreach ($videoInfo['source'] as $type => $url) {
            if (!empty($url)) {
                $output .= '<source src="' . $url . '" type="video/' . $type . '">';
            }
        }
        $output .= ' </video>';

        return $output;
    }

    // Videos from external services
    $output = '<div class="video-container"><iframe width="' . $width . '" height="'
        . $height . '" src="' . $videoInfo['embedUrl']
        . '" frameborder="0" allowfullscreen></iframe></div>';

    return $output;
}
