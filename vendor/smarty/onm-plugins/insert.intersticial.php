<?php

/**
 * Smarty plugin to render a intersticial banner into OpenNeMas
 *
 * @param array $params
 * @param Template $tpl Template class which extends of Smarty
 * @param boolean $cache Use cache
 * @todo Pasar toda logica a una clase con metodos para renderizar los distintos tipos de banners
*/
use Onm\Settings as s;

function smarty_insert_intersticial($params, &$smarty)
{
    // nested function to render intersticial
    // FIXME: include function into Advertisement static method
    if (!function_exists('renderOutput')) {
        function renderOutput($content, $banner, $params)
        {
            if (is_object($banner)
                && (($banner->type_advertisement + 50) % 100) == 0
            ) {
                $content     = json_encode($content);
                $content     = str_replace('\n', '', $content);
                $content     = preg_replace('/[ ][ ]+/', ' ', $content);
                $timeout     = intval($banner->timeout) * 1000; // convert to ms
                $adsSettings = s::get('ads_settings');
                $minutesExpire  = 1;
                if (!empty($adsSettings['lifetime_cookie'] )) {
                    $minutesExpire = $adsSettings['lifetime_cookie']; // convert to days
                    // $daysExpire = number_format($daysExpire, 2);
                }
                $pk_advertisement = date('YmdHis', strtotime($banner->created)).
                                    sprintf('%06d', $banner->pk_advertisement);

                // Fetch categoryId to generate distinct cookie for sections
                if (isset($params['category'])) {
                    $ccm = new ContentCategoryManager();
                    $categoryId = $ccm->get_id($params['category']);
                } else {
                    $categoryId = 0;
                }
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
            cookieName: "ib_$pk_advertisement-$categoryId",
            content: $content,
            daysExpire: $minutesExpire,
            timeout: $timeout
        });

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
    if (empty($type)
        || (defined('ADVERTISEMENT_ENABLE') && !ADVERTISEMENT_ENABLE)
    ) {
        // Banners not enabled
        return $output;
    }

    $advertisement = Advertisement::getInstance();

    $banner = $advertisement->fetch('banner' . $type);
    $photo  = $advertisement->fetch('photo' . $type);

    if (isset($photo)) {
        $width  = $photo->width;
        $height = $photo->height;
    } else {
        $width  = '100%';
        $height = '100%';
    }

    // Use vars instead of constants (synchronize)
    $siteUrl         = SITE_URL;
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
        $mediaImgPathWeb = $cm->getUrlContent($wsUrl.'/ws/instances/mediaurl/', true);
    }

    // Overlap flash?
    $overlap  = (isset($params['overlap']))? $params['overlap']: false;
    //$overlap  = (isset($params['cssclass']))? $params['cssclass']: 'wrapper_intersticial';
    $isBastardIE = preg_match('/MSIE /', $_SERVER['HTTP_USER_AGENT']);

    if (isset($params['beforeHTML'])) {
        $output .= $params['beforeHTML'];
    }

    $cssclass = (isset($params['cssclass'])? $params['cssclass'] : '');
    // Initial container
    $output .= '<div class="'.$cssclass.'" align="center">';

    if (isset($banner->with_script) && $banner->with_script == 1) {
        // Original method
        // $output .= $banner->script;
        // Parallelized method using iframes

        if (preg_match('/iframe/', $banner->script)) {
            $output .= $banner->script;
        } else {
            $url = $siteUrl.'ads/get/'
                . date('YmdHis', strtotime($banner->created))
                . sprintf('%06d', $banner->pk_content)  . '.html" ';
            $output .= '<iframe src="'.$url.'"'
               .' style="width:'.$banner->params['width'].'px; height:'.$banner->params['height'].'px" '
               .'></iframe>';
        }

    } elseif (!empty($banner->pk_advertisement)) {

        if (strtolower($photo->type_img)=='swf') {
            if (!$overlap && !$banner->overlap) {
                // Flash object
                // FIXME: build flash object with all tags and params
                $output .= '<a target="_blank" href="'.$siteUrl.'/ads/'.
                            date('YmdHis', strtotime($banner->created)).
                            sprintf('%06d', $banner->pk_content) .
                            '.html" rel="nofollow">';
                $output .= '<object>
                        <param name="wmode" value="opaque" />
                        <param name="movie" value="'. $mediaImgPathWeb. $photo->path_file. $photo->name. '" />
                        <param name="width" value="'.$width.'" />
                        <param name="height" value="'.$height.'" />
                        <embed src="'. $mediaImgPathWeb. $photo->path_file. $photo->name. '"
                            width="'.$width.'" height="'.$height.'" alt="Publicidad '. $banner->title. '"></embed>
                    </object>';
                $output .= '</a>';
            } else {

                if (!$isBastardIE) {
                    $output .= '<div style="position:relative;width:'.$width.'px;height:'.$height.'px;">
                        <div style="left:0px;top:0px;cursor:pointer;'
                            .'background-color:transparent;position:absolute;z-index:3344;
                            width:'.$width.'px;height:'.$height.'px;"></div>';
                } else {
                    $output .= '<div style="position:relative;width:'.$width.'px;height:'.$height.'px;">
                        <div style="left:0px;top:0px;cursor:pointer;'
                            .'background-color:#FFF;filter:alpha(opacity=0);position:absolute;z-index:3344;
                            width:'.$width.'px;height:'.$height.'px;"></div>';
                }

                $output .= '<div style="position: absolute; z-index: 0; width: '
                            .$width.'px; left: 0px; margin: 0 auto;">
                        <object width="'.$width.'" height="'.$height.'">
                            <param name="wmode" value="opaque" />
                            <param name="movie" value="'. $mediaImgPathWeb. $photo->path_file. $photo->name. '" />
                            <param name="wmode" value="opaque" />
                            <param name="width" value="'.$width.'" />
                            <param name="height" value="'.$height.'" />
                            <embed src="'. $mediaImgPathWeb. $photo->path_file. $photo->name. '" wmode="opaque"
                                width="'.$width.'" height="'.$height.'" alt="Publicidad '. $banner->title. '"></embed>
                        </object>
                    </div>
                  </div>';

                if (isset($params['afterHTML'])) {
                    $output .= $params['afterHTML'];
                }
            }
        } else {
            // Image
            $output .= '<a target="_blank" href="'.$siteUrl.'/ads/'.
                        date('YmdHis', strtotime($banner->created)).
                        sprintf('%06d', $banner->pk_content) .'.html" rel="nofollow">';
            $output .= '<img src="'. $mediaImgPathWeb. $photo->path_file. $photo->name.'"
                    alt="Publicidad '.$banner->title.'" width="'.$width.'" height="'.$height.'" />';
            $output .= '</a>';
        }
    } else {
        // Empty banner, don't return anything
        $output = '';

        return renderOutput($output, $banner, $params);
    }

    $output .= '</div>';

    // Post content of banner
    if (isset($params['afterHTML'])) {
        $output .= $params['afterHTML'];
    }

    return renderOutput($output, $banner, $params);
}
