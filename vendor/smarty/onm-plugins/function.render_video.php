<?php
function smarty_function_render_video($params, &$smarty)
{
    $video = $params['video'];
    $cssClass = (array_key_exists('css_class', $params) ? $params['css'] : null);

    $output ='';
    if ($video->author_name == 'internal') {

        $rand = rand();
        $output = '<a class="flashplayer" href="'.$params['base_url'].DS.$video->video_url.'" style="display:block;width:'
                  .$params['width'].'px;height:'.$params['height'].'px;background: url(\''.$video->thumb.'\')" id="flashplayer-'.$rand.'"></a>'."\n";

        $output .= '
		<script type="text/javascript" defer="defer">
			flowplayer("flashplayer-'.$rand.'", "'.SITE_URL.'media/common_assets/fplayer/flowplayer-3.2.7.swf",
                {
                    onLoad: function() { this.setVolume(30); },
                    clip: { autoPlay: false },
                    screen: {"height":"100pct","top":0 },
                    plugins: { "controls":{ "buttonOffColor":"rgba(130,130,130,1)", "borderRadius":"0px", "timeColor":"#ffffff", "bufferGradient":"none", "sliderColor":"#000000", "zIndex":1, "backgroundColor":"rgba(0, 0, 0, 0)", "scrubberHeightRatio":0.6, "volumeSliderGradient":"none", "tooltipTextColor":"#ffffff", "sliderGradient":"none","spacing":{"time":6,"volume":8,"all":2},  "timeBorderRadius":20,  "timeBgHeightRatio":0.8, "volumeSliderHeightRatio":0.6,"progressGradient":"none","height":26,"volumeColor":"#4599ff","tooltips":{"marginBottom":5,"buttons":false, "fullscreen": "Ver a pantalla completa"},"timeSeparator":" ","name":"controls","volumeBarHeightRatio":0.2,"opacity":1,"timeFontSize":12,"left":"50pct","tooltipColor":"rgba(0, 0, 0, 0)","bufferColor":"#a3a3a3","border":"0px","volumeSliderColor":"#ffffff","buttonColor":"#ffffff","durationColor":"#b8d9ff","autoHide":{"enabled":true,"hideDelay":500,"hideStyle":"fade","mouseOutDelay":500,"hideDuration":400,"fullscreenOnly":false},"backgroundGradient":"none","width":"100pct","display":"block","sliderBorder":"1px solid rgba(128, 128, 128, 0.7)","buttonOverColor":"#ffffff","url":"flowplayer.controls-3.2.5.swf","timeBorder":"0px solid rgba(0, 0, 0, 0.3)","progressColor":"#4599ff","timeBgColor":"rgb(0, 0, 0, 0)","scrubberBarHeightRatio":0.2,"bottom":0,"builtIn":false,"volumeBorder":"1px solid rgba(128, 128, 128, 0.7)","margins":[2,12,2,12] },},
                }
            );
		</script>';

    } elseif ($video->author_name == 'script') {
        $output = '<div class="video-container">'.$video->body.'</div>';

    } elseif ($video->author_name == 'external') {
        if (!empty($video->video_url)) {
            $output = "<video class='{$cssClass}' controls>";
                $output .= '<source src="'.$video->video_url.'" type="video/flv">';
            $output .= ' </video>';

        } elseif (!empty($video->information)) {
            if (is_array($video->information)) {
                $videoInfo = $video->information;
            } else {
                $videoInfo = unserialize($video->information);
            }
            if (!empty($videoInfo['source'])) {
                $output = '<video controls>';
                foreach ($videoInfo['source'] as $type => $url) {
                    if (!empty($url)) {
                        $output .= '<source src="'.$url.'" type="video/'.$type.'">';
                    }
                }
                $output .= ' </video>';
            }
        }

    } else {
        if (is_array($video->information)) {
            $videoInfo = $video->information;
        } else {
            $videoInfo = unserialize($video->information);
        }

        if ($video->author_name == 'Youtube' || $videoInfo['service'] == 'Youtube') {

            $videoUrl = $video->video_url;

            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoUrl, $match)) {
                $videoId = $match[1];
                if (!empty($videoId)) {
                    $videoUrl = '//www.youtube.com/embed/'.$videoId;
                }
            }
            $width  = '560';
            $height = '320';
            if ($params['width'] || $params['height']) {
                $width  = $params['width'];
                $height = $params['height'];
            }
            $output = '<div class="video-container"><iframe width="'.$width.'" height="'.$height.'" src="'.$videoUrl.'" frameborder="0" allowfullscreen></iframe></div>';

        } else {

            if ($params['width'] || $params['height']) {
                $videoInfo['embedHTML'] = preg_replace("@width='\d*'@", "width='{$params['width']}'", $videoInfo['embedHTML']);
                $videoInfo['embedHTML'] = preg_replace("@height='\d*'@", "height='{$params['height']}'", $videoInfo['embedHTML']);
            }
            $output = $videoInfo['embedHTML'];
        }

    }

    return $output;
}
