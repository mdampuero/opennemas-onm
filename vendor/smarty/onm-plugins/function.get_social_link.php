<?php
/*
 * -------------------------------------------------------------
 * File:        function.get_social_link.php
 * Fetches from settings the selected social link and returns the url
 * -------------------------------------------------------------
 */
use Onm\Settings as s;

function smarty_function_get_social_link($params, &$smarty)
{

    if (empty($params['page'])) {
        trigger_error("[plugin] get_social_link parameter 'page' cannot be empty", E_USER_NOTICE);
        return;
    }

    $style ='';
    if (!isset($params['style'])) {
        $style .= ' style = "'. $params['style'].'"';
    }

    $output = '';
    switch ($params['page']) {
        case 'twitter':
            if (!isset($params['img'])) {
                $params['img'] = $smarty->parent->image_dir.'bullets/twitter_32.png';
            }

            $twitterPage = s::get('twitter_page');

            if (!empty($twitterPage)) {
                $output =
                '<li '.$style.'>
                    <a href="'.$twitterPage.'" target="_blank" title="Visita nuestro perfíl en Twitter">
                       <img src="'.$params['img'].'" alt="" />
                    </a>
                </li>';
            }
            break;
        case 'facebook':
            if (!isset($params['img'])) {
                $params['img'] = $smarty->parent->image_dir.'bullets/facebook_32.png';
            }

            $facebookPage = s::get('facebook_page');

            if (!empty($facebookPage)) {
                $output =
                '<li '.$style.'>
                    <a href="'.$facebookPage.'" target="_blank" title="Visita nuestro perfíl en Facebook">
                       <img src="'.$params['img'].'" alt="" />
                    </a>
                </li>';
            }
            break;
        case 'google':
            if (!isset($params['img'])) {
                $params['img'] = $smarty->parent->image_dir.'bullets/google-plus-32.png';
            }

            $googlePage = s::get('google_page');

            if (!empty($googlePage)) {
                $output =
                    '<li '.$style.'>
                        <a href="'.$googlePage.'" target="_blank" title="Visita nuestro perfíl en Google Plus">
                           <img src="'.$params['img'].'" alt="" />
                        </a>
                    </li>';
            }
            break;
        case 'youtube':
            if (!isset($params['img'])) {
                $params['img'] = $smarty->parent->image_dir.'bullets/youtube-24.png';
            }

            $youtubePage = s::get('youtube_page');

            if (!empty($googlePage)) {
                $output =
                    '<li '.$style.'>
                        <a href="'.$youtubePage.'" target="_blank" title="Visita nuestra página en Youtube">
                           <img src="'.$params['img'].'" alt="" />
                        </a>
                    </li>';
            }
            break;
        default:
            if (!isset($params['img'])) {
                $output = '';
            }
            if (!empty($params['page']) && !empty($params['img'])) {
                $output =
                '<li '.$style.'>
                    <a href="'.$params['page'].'" target="_blank" title="Visitanos">
                       <img src="'.$params['img'].'" alt="" />
                    </a>
                </li>';

            }
            break;
    }

    return $output;
}

