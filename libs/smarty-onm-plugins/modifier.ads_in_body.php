<?php
/**
 * Splits body into paragraphs and renders slots for advertisements between
 * them.
 *
 * @param string $body The body to split.
 * @param string $type The content type name.
 *
 * @return type Description
 */
function smarty_modifier_ads_in_body($body, $contentType = 'article')
{
    // Split body in paragraphs
    preg_match_all('/(.*?)<\/p>/s', $body, $matches);

    if (empty($matches[0])) {
        return $body;
    }

    // Clean empty paragraphs
    $paragraphs = array_filter($matches[0], function ($a) {
        return !in_array($a, ['<p>&nbsp;</p>', '<p></p>']);
    });

    // Id for articles
    $id = 2200;

    // Id for opinions
    if ($contentType === 'opinion') {
        $id = 3200;
    }

    $bodyWithAds = [];

    $safeFrame = getService('core.helper.advertisement')->isSafeFrameEnabled();
    if (!$safeFrame) {
        $adsRepository = getService('advertisement_repository');
        switch ($contentType) {
            case 'article':
                $ads = $adsRepository->findByPositionsAndCategory(
                    [ 2201,2202,2203,2204,2205,2206,2207,2208,2209,2210,2211 ],
                    $category
                );
                break;
            case 'opinion':
                $ads = $adsRepository->findByPositionsAndCategory(
                    [ 3201,3202,3203,3204,3205,3206,3207,3208,3209,3210,3211 ],
                    4
                );
                break;
        }

        $adsRenderer    = getService('core.renderer.advertisement');
        foreach ($paragraphs as $key => $paragraph) {
            $positionID = $id + 1 + $key;

            $bodyWithAds[] = $paragraph;
            $adsForPosition = array_filter($ads, function($el) use ($positionID) {
                return (int) $el->type_advertisement == $positionID;
            });
            if (count($adsForPosition) < 1) {
                continue;
            }
            $ad = $adsForPosition[array_rand($adsForPosition)];

            $bodyWithAds[] = $ad->render([]);
        }
    } else {
        $html = '<div class="ad-slot oat" data-type="%s"></div>';

        foreach ($paragraphs as $key => $paragraph) {
            $slot   = '';
            $slotId = $id + 1 + $key;

            $bodyWithAds[] = $paragraph;
            $bodyWithAds[] = sprintf($html, $slotId);
        }
    }

    return implode('', $bodyWithAds);
}
