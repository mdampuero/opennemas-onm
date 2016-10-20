<?php
/*
 * Insert custom ads between body paragraphs
 */
function smarty_modifier_ads_in_body($body)
{
    // Get inBody ads
    $ads = \Advertisement::findForPositionIdsAndCategory(
        [
            2201,2202,2203,2204,2205,2206,2207,2208,2209,2210,2211,
            3201,3202,3203,3204,3205,3206,3207,3208,3209,3210,3211
        ],
        0
    );

    if (empty($ads)) {
        return $body;
    }

    // Split body in paragraphs and return id empty
    preg_match_all('/<p.*?>(.*?)<\/p>/', $body, $matches);
    if (empty($matches[0])) {
        return $body;
    }

    // Clean empty paragraphs
    $paragraphs = array_merge(
        array_diff($matches[0], ['<p>&nbsp;</p>', '<p></p>'])
    );

    // Insert defined ads on it's positions
    $bodyWithAds = '';
    foreach ($paragraphs as $key => $paragraph) {
        $bodyWithAds .= $paragraph;
        switch ($key) {
            case '0': // After 1st paragraph
                $ad = (array_key_exists(2201, $ads)) ? $ads[2201]: null;
                $ad = (array_key_exists(3201, $ads)) ? $ads[3201]: null;
                break;
            case '1': // After 2st paragraph
                $ad = (array_key_exists(2202, $ads)) ? $ads[2202]: null;
                $ad = (array_key_exists(3202, $ads)) ? $ads[3202]: null;
                break;
            case '2': // After 3rd paragraph
                $ad = (array_key_exists(2203, $ads)) ? $ads[2203]: null;
                $ad = (array_key_exists(3203, $ads)) ? $ads[3203]: null;
                break;
            case '3': // After 4th paragraph
                $ad = (array_key_exists(2204, $ads)) ? $ads[2204]: null;
                $ad = (array_key_exists(3204, $ads)) ? $ads[3204]: null;
                break;
            case '4': // After 5th paragraph
                $ad = (array_key_exists(2205, $ads)) ? $ads[2205]: null;
                $ad = (array_key_exists(3205, $ads)) ? $ads[3205]: null;
                break;
            case '5': // After 6th paragraph
                $ad = (array_key_exists(2206, $ads)) ? $ads[2206]: null;
                $ad = (array_key_exists(3206, $ads)) ? $ads[3206]: null;
                break;
            case '6': // After 7th paragraph
                $ad = (array_key_exists(2207, $ads)) ? $ads[2207]: null;
                $ad = (array_key_exists(3207, $ads)) ? $ads[3207]: null;
                break;
            case '7': // After 8th paragraph
                $ad = (array_key_exists(2208, $ads)) ? $ads[2208]: null;
                $ad = (array_key_exists(3208, $ads)) ? $ads[3208]: null;
                break;
            case '8': // After 9th paragraph
                $ad = (array_key_exists(2209, $ads)) ? $ads[2209]: null;
                $ad = (array_key_exists(3209, $ads)) ? $ads[3209]: null;
                break;
            case '9': // After 10th paragraph
                $ad = (array_key_exists(2210, $ads)) ? $ads[2210]: null;
                $ad = (array_key_exists(3210, $ads)) ? $ads[3210]: null;
                break;
            case '10': // After 11th paragraph
                $ad = (array_key_exists(2211, $ads)) ? $ads[2211]: null;
                $ad = (array_key_exists(3211, $ads)) ? $ads[3211]: null;
                break;
            default:
                $ad = null;
                break;
        }
        if (!is_null($ad)) {
            $bodyWithAds .= $ad->render([
                'width'      => $ad->width,
                'height'     => $ad->height,
                'beforeHTML' => '<div id="ad-'.$ad->id.'" class="ad_in_column ad_horizontal_marker" style="text-align:center;">',
                'afterHTML'  => '</div>',
            ]);
        }
    }

    return $bodyWithAds;
}
