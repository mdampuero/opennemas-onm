<?php

use Onm\Settings as s;

/**
 * Smarty plugin to render a banner into OpenNeMas
 *
 * @param array $params
 * @param Template $tpl Template class which extends of Smarty
 * @param boolean $cache Use cache
 * @todo Pasar toda logica a una clase con metodos para renderizar los distintos tipos de banners
*/
function smarty_insert_renderbanner($params, &$smarty)
{
    // nested function to render intersticial
    // FIXME: include function into Advertisement static method
    if (!function_exists('render_output')) {
        function render_output($content, $banner)
        {
            if (is_object($banner)
                && property_exists($banner, 'type_advertisement')
                && ((($banner->type_advertisement + 50)%100) == 0)
            ) {
                $content = json_encode($content);

                $timeout = intval($banner->timeout) * 1000; // convert to ms
                $pk_advertisement = date('YmdHis', strtotime($banner->created)).
                                    sprintf('%06d', $banner->pk_advertisement);

                /*
                 * intersticial = new IntersticialBanner({iframeSrc: '/sargadelos.html?cacheburst=1254325526',
                 *                                        timeout: -1,
                 *                                        useIframe: true});
                 */
                $output = <<< JSINTERSTICIAL
<script type="text/javascript" language="javascript">
/* <![CDATA[ */
    var intersticial = new IntersticialBanner({
        publiId: "$pk_advertisement",
        cookieName: "ib_$pk_advertisement",
        content: $content,
        timeout: $timeout});
/* ]]> */
</script>
JSINTERSTICIAL;

                return $output;
            }

            return $content;
        }
    }

    $output = '';
    $type = $params['type'];
    if (empty($type) || (defined('ADVERTISEMENT_ENABLE') && !ADVERTISEMENT_ENABLE)) {
        // Banners not enabled
        return $output;
    }

    $advertisement = Advertisement::getInstance();
    $banner = $advertisement->fetch('banner' . $type);
    $photo  = $advertisement->fetch('photo' . $type);

    if (!isset($params['cssclass'])) {
        $smarty->trigger_error("renderbanner: missing 'cssclass' parameter");

        return;
    }
    $cssclass = $params['cssclass'];

    // Extract width and height properties from CSS
    if (!isset($params['width']) || !isset($params['height'])) {
        $width  = preg_replace('/^[a-z]+(\d+)x\d+$/i', '\1', $cssclass);
        $height = preg_replace('/^[a-z]+\d+x(\d+)$/i', '\1', $cssclass);
    } else {
        $width  = $params['width'];
        $height = $params['height'];
    }

    /**
     * If the Ad is Flash/Image based try to get the width and height fixed
     */
    if (isset ($photo)) {
        if (($photo->width <= $width)
             && ($photo->height <= $height)
        ) {
            $width = $photo->width;
            $height = $photo->height;
        }
    }

    // Use vars instead of constants (synchronize)
    $siteUrl         = SITE_URL;
    $mediaImgPath    = MEDIA_IMG_PATH;
    $mediaImgPathWeb = MEDIA_IMG_PATH_WEB;

    // Check if is synchronized advertisements
    if (preg_match('@/extseccion/@', $_SERVER['REQUEST_URI']) ||
        preg_match('@/extarticulo/@', $_SERVER['REQUEST_URI'])) {
        // Get sync params
        $wsUrl = '';
        $syncParams = s::get('sync_params');
        foreach ($syncParams as $siteUrl => $categoriesToSync) {
            foreach ($categoriesToSync as $value) {
                if (preg_match('/'.$smarty->tpl_vars['category_name']->value.'/i', $value)) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        $cm = new \ContentManager;

        $siteUrl         = $cm->getUrlContent($wsUrl.'/ws/instances/siteurl/', true);
        $mediaImgPath    = $cm->getUrlContent($wsUrl.'/ws/instances/mediaImgPath/', true);
        $mediaImgPathWeb = $cm->getUrlContent($wsUrl.'/ws/instances/mediaurl/', true);
    }

    // If $height is equals to * then calculate using GD
    if ($height == '*') {
        if (file_exists($mediaImgPath. $photo->path_file. $photo->name)) {
            list($w, $h, $type, $attr) = getimagesize($mediaImgPath. $photo->path_file. $photo->name);

            // La imagen es proporcional
            if ($w == $width) {
                $height = $h;
            } else {
                if ($w != 0) {
                    $height = floor(($width*$h)/$w);
                } else {
                    $height = 0;
                }
            }
        } else {
            $height = 0;
        }
    }

    // Overlap flash?
    $overlap  = (isset($params['overlap']))? $params['overlap']: false;
    $isBastardIE = preg_match('/MSIE /', $_SERVER['HTTP_USER_AGENT']);

    if (isset($params['beforeHTML'])) {
        $output .= $params['beforeHTML'];
    }

    // Initial container
    $output .= '<div class="'.$cssclass.'">';

    if (isset($banner->with_script) && $banner->with_script == 1) {
        // Original method
        // $output .= $banner->script;
        // Parallelized method using iframes
        if (preg_match('/iframe/', $banner->script)) {
            $output .= $banner->script;
        } else {
            $output .= '<iframe src="'.$siteUrl.'ads/get/'
                .date('YmdHis', strtotime($banner->created)).sprintf('%06d', $banner->pk_content)  . '.html" ' .
               'scrolling="no" frameborder="0" width="' . $width . '" height="' . $height . '">Publicidad</iframe>';
        }

    } elseif (!empty($banner->pk_advertisement)) {

        // TODO: controlar los banners swf especiales con div por encima
        if (strtolower($photo->type_img)=='swf') {

            if (!$overlap && !$banner->overlap) {
                // Flash object
                // FIXME: build flash object with all tags and params

                $output .= '<a target="_blank" href="'
                    .$siteUrl.'ads/'. date('YmdHis', strtotime($banner->created))
                    .sprintf('%06d', $banner->pk_advertisement)
                    .'.html" rel="nofollow" style="display:block;cursor:pointer">';
                $output .= '<object width="'.$width.'" height="'.$height.'" >
                        <param name="wmode" value="transparent" />
                        <param name="movie" value="'. $mediaImgPathWeb. $photo->path_file. $photo->name. '" />
                        <param name="width" value="'.$width.'" />
                        <param name="height" value="'.$height.'" />
                        <embed src="'. $mediaImgPathWeb. $photo->path_file. $photo->name. '"
                            width="'.$width.'" height="'.$height.'" alt="Publicidad '.
                            $banner->title. '" wmode="transparent"></embed>
                    </object>';
            } else {
                if (!$isBastardIE) {
                    $output .= '<div style="position: relative; width: '.$width.'px; height: '.$height.'px;">
                        <div style="left:0px;top:0px;cursor:pointer;'
                            .'background-color:transparent;position:absolute;z-index:100;width:'.
                            $width.'px;height:'.$height.'px;"
                            onclick="javascript:window.open(\''.$siteUrl.'ads/'
                            .date('YmdHis', strtotime($banner->created))
                            .sprintf('%06d', $banner->pk_advertisement).'.html\', \'_blank\');return false;"></div>';
                } else {
                    $output .= '<div style="position: relative; width: '.$width.'px; height: '.$height.'px;">
                        <div style="left:0px;top:0px;cursor:pointer;opacity:0;'
                            .'background-color:#FFF;filter:alpha(opacity=0);position:absolute;z-index:100;width:'.
                            $width.'px;height:'.$height.'px;"
                            onclick="javascript:window.open(\''.$siteUrl.'ads/'
                                .date('YmdHis', strtotime($banner->created))
                                .sprintf('%06d', $banner->pk_advertisement)
                                .'.html\', \'_blank\');return false;"></div>';
                }

                $output .= '<div style="position: absolute; z-index: 0; width: '.$width.'px; left: 0px;">
                        <object width="'.$width.'" height="'.$height.'">
                            <param name="movie" value="'. $mediaImgPathWeb. $photo->path_file. $photo->name. '" />
                            <param name="wmode" value="opaque" />
                            <param name="width" value="'.$width.'" />
                            <param name="height" value="'.$height.'" />
                            <embed src="'. $mediaImgPathWeb. $photo->path_file. $photo->name. '" wmode="opaque"
                                width="'.$width.'" height="'.$height.'" alt="Publicidad '. $banner->title. '"></embed>
                        </object>
                    </div>
                  </div>';

                $output .= '</div>';

                if (isset($params['afterHTML'])) {
                    $output .= $params['afterHTML'];
                }

                return render_output($output, $banner);
            }
        } else {
            // Image
            $output .= '<a target="_blank" href="'.$siteUrl.'ads/'.
                     date('YmdHis', strtotime($banner->created)).
                     sprintf('%06d', $banner->pk_advertisement) .'.html" rel="nofollow">';
            $output .= '<img src="'. $mediaImgPathWeb. $photo->path_file. $photo->name.'"
                    alt="Publicidad '.$banner->title.'" width="'.$width.'" height="'.$height.'" />';
        }

        $output .= '</a>';
    } else {
        // Empty banner, don't return anything
        $output = '';

        return render_output($output, $banner);
    }

    $output .= '</div>';

    // Post content of banner
    if (isset($params['afterHTML'])) {
        $output .= $params['afterHTML'];
    }

    return render_output($output, $banner);
}
