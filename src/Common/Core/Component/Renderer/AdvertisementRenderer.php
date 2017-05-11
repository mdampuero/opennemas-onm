<?php
namespace Common\Core\Component\Renderer;

/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class AdvertisementRenderer
{
    /**
     * Initializes the renderer
     * @param  Templating $template the tempalte service object
     **/
    public function __construct($template)
    {
        $this->template = $template;
    }
    /**
     * Renders an advertisement given and $ad
     *
     * @param  Advertisement $ad the ad to render
     *
     * @return string the HTML for the slot
     **/
    public function render(\Advertisement $ad)
    {
        $content = '';
        if ($ad->with_script == 1) {
            $content = $ad->script;
        } elseif ($ad->with_script == 2) {
            $content = $this->renderReviveSlot($ad);
        } elseif ($ad->with_script == 3) {
            $content = $this->renderDFPSlot($ad);
        } else {
            $content = $this->renderImage($ad);
        }

        return $content;
    }

    /**
     * Renders a DFP slot given a Advertisement
     *
     * @param  Advertisement $ad the ad to render
     *
     * @return string the HTML content for the DFP slot
     **/
    public function renderDFPSlot($ad)
    {
        return "<div id='zone_{$ad->pk_advertisement}'>"
           ."<script type='text/javascript'>"
           ."googletag.cmd.push(function() { googletag.display('zone_{$ad->pk_advertisement}'); });"
           ."</script></div>";
    }

    /**
     * Generates the HTML header section for the DFP ads
     *
     * @param  arrray  the list of advertisements to generate the header from
     *
     * @return string the HTML content for the DFP slot
     **/
    public function renderDFPHeader($ads, $params)
    {
        $headerContents = '';
        $dfpZonesInformation = [];
        foreach ($ads as $advertisement) {
            if ($advertisement->with_script == 3
                && array_key_exists('googledfp_unit_id', $advertisement->params)
                && !empty($advertisement->params['googledfp_unit_id'])
            ) {
                // TODO: Check Api/AdvertisementController::getSizes.
                $sizes = array_map(function ($a) {
                    return "[ {$a['width']}, {$a['height']} ]";
                }, $advertisement->params['sizes']);

                $sizes     = "[".implode(', ', $sizes)."]";
                $dfpUnitID = $advertisement->params['googledfp_unit_id'];
                $adId      = $advertisement->id;

                $dfpZonesInformation []= "  googletag.defineSlot('{$dfpUnitID}', {$sizes}, 'zone_{$adId}').addService(googletag.pubads());";
            }
        }

        if (count($dfpZonesInformation) > 0) {
            // Check if targeting is set
            $dfpOptions = getService('setting_repository')->get('dfp_options');
            $targetingCode = '';
            if (is_array($dfpOptions) &&
                array_key_exists('target', $dfpOptions) &&
                !empty($dfpOptions['target'])
            ) {
                $targetingCode = "googletag.pubads().setTargeting('".$dfpOptions['target']."', ['".$params['category']."']);";
            }
            if (is_array($dfpOptions) &&
                array_key_exists('module', $dfpOptions) &&
                !empty($dfpOptions['module'])
            ) {
                $module = '';
                $content = $params['content'];
                if (!is_null($content)) {
                    $module = $content->content_type_name;
                } elseif ($params['x-tags']->value) {
                    $xTags = $params['x-tags']->value;
                    $module = ($xTags == 'frontpage-page,home') ? 'home' : strtok($xTags, ',');
                } elseif (!empty($params['polls']->value)) {
                    $module = 'poll-frontpage';
                }
                $targetingCode .= "\ngoogletag.pubads().setTargeting('".$dfpOptions['module']."', ['".$module."']);\n";
            }

            // Check for custom code
            $dfpCustomCode = getService('setting_repository')->get('dfp_custom_code');
            $customCode = '';
            if (!empty($dfpCustomCode)
            ) {
                $customCode = "\n".base64_decode($dfpCustomCode)."\n";
            }

            $headerContents = "<script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>\n"
                ."<script>\n"
                ."var googletag = googletag || {};\n"
                ."googletag.cmd = googletag.cmd || [];\n"
                ."googletag.cmd.push(function() {\n"
                .implode("\n", $dfpZonesInformation)
                .$targetingCode
                .$customCode
                ."\n  googletag.pubads().enableSingleRequest();\n"
                ."  googletag.pubads().collapseEmptyDivs();\n"
                ."  googletag.enableServices();\n"
                ."});\n</script>".PHP_EOL;
        }

        return $headerContents;
    }

    /**
     * Renders a Revive slot given a Advertisement
     *
     * @param  Advertisement $ad the ad to render
     *
     * @return string the HTML content for the DFP slot
     **/
    public function renderReviveSlot($ad)
    {
        if (in_array($ad->type_advertisement, array(50,150,250,350,450,550))) {
            $url = url('frontend_ad_get', array('id' => $ad->pk_content));
            $content = '<iframe src="'.$url.'" style="width:800px; max-width:100%; height:600px; overflow: hidden;border:none" '.
            'scrolling="no" ></iframe>';
        } else {
            $content = "<script type='text/javascript' data-id='{$ad->id}'><!--// <![CDATA[
            OA_show('zone_{$ad->id}');
            // ]]> --></script>";
        }

        return $ad;
    }

    /**
     * Generates the HTML header section for the Revive ads
     *
     * @param  arrray  the list of advertisements to generate the header from
     *
     * @return string the HTML content for the Revive slot
     **/
    public function renderReviveHeader($ads, $params)
    {
        $reviveZonesInformation = [];

        foreach ($ads as $advertisement) {
            if ($advertisement->with_script == 2
                && array_key_exists('openx_zone_id', $advertisement->params)
                && !empty($advertisement->params['openx_zone_id'])
            ) {
                $reviveZonesInformation []= " 'zone_{$advertisement->id}' : ".(int) $advertisement->params['openx_zone_id'];
            }
        }

        // Generate revive ads positions
        if (count($reviveZonesInformation) > 0 && count($adsReviveConfs) > 0) {
            $adsReviveConfs = getService('setting_repository')->get('revive_ad_server');

            $reviveAdsPositions = "\n<script type='text/javascript'><!--// <![CDATA["
                ."var OA_zones = { \n".implode(",\n", $reviveZonesInformation)."\n}"
                ."// ]]> --></script>"
                ."<script type='text/javascript' src='{$adsReviveConfs['url']}/www/delivery/spcjs.php?cat_name={$actual_category}'></script>";

            $output = str_replace('</head>', $reviveAdsPositions.'</head>', $output);
        }
    }

    /**
     * Renders an image/swf slot
     *
     * @param string $ad the ad object to render from
     *
     * @return string the HTML code for the ad image
     **/
    public function renderImage($ad)
    {
        return '<div>image ad not implemented yet</div>';
        // TODO: Load photo
        $photo = null;

        // If the Ad is Flash/Image based try to get the width and height fixed
        if (isset($photo)) {
            if (($photo->width <= $width)
                && ($photo->height <= $height)
            ) {
                $width  = $photo->width;
                $height = $photo->height;
            }
        }

        // TODO: controlar los banners swf especiales con div por encima
        if (strtolower($photo->type_img) == 'swf') {
            // Generate flash object with wmode transparent
            $flashObject =
                '<object width="'.$width.'" height="'.$height.'" >
                    <param name="wmode" value="transparent" />
                    <param name="movie" value="'.$mediaUrl. '" />
                    <param name="width" value="'.$width.'" />
                    <param name="height" value="'.$height.'" />
                    <embed src="'. $mediaUrl. '" width="'.$width.'" height="'.$height.'" '
                        .'SCALE="exactfit" wmode="transparent"></embed>
                </object>';
        } else {
            // Image
            $imageObject = '<img alt="'.$photo->category_name.'" src="'. $mediaUrl.'" '
                            .'width="'.$width.'" height="'.$height.'" />';

            $content = '<a target="_blank" href="'.$url.'" rel="nofollow">'.$imageObject.'</a>';
        }
    }
}
