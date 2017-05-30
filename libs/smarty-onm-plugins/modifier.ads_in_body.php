<?php
/**
 * Splits body into paragraphs and renders slots for advertisements before them.
 *
 * @param string $body The body to split.
 * @param string $type The content type name.
 *
 * @return string The body with advertisements.
 */
function smarty_modifier_ads_in_body($body, $contentType = 'article')
{
    // Split body into paragraphs
    preg_match_all('/(.*?)<\/p>/s', $body, $matches);

    if (empty($matches[0])) {
        return $body;
    }

    // Clean empty paragraphs
    $paragraphs = array_filter($matches[0], function ($a) {
        return !in_array($a, ['<p>&nbsp;</p>', '<p></p>']);
    });

    $id  = $contentType === 'opinion' ? 3200 : 2200;
    $ads = getService('core.template')->getSmarty()
        ->tpl_vars['advertisements']->value;

    $bodyWithAds = [];
    $safeFrame   = getService('core.helper.advertisement')->isSafeFrameEnabled();
    $renderer    = getService('core.renderer.advertisement');
    $html        = '<div class="ad-slot oat" data-type="%s"></div>';

    foreach ($paragraphs as $key => $paragraph) {
        $slotId        = $id + 1 + $key;
        $bodyWithAds[] = $paragraph;
        $ad            = sprintf($html, $slotId);

        if (!$safeFrame) {
            $adsForPosition = array_filter($ads, function ($a) use ($slotId) {
                return (int) $a->type_advertisement == $slotId;
            });

            if (count($adsForPosition) < 1) {
                continue;
            }

            $ad = $adsForPosition[array_rand($adsForPosition)];
            $ad = $renderer->render($ad);
        }

        $bodyWithAds[] = $ad;
    }

    return implode('', $bodyWithAds);
}
