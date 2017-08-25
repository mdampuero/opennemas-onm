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
        return !in_array(trim($a), ['<p>&nbsp;</p>', '<p></p>']);
    });

    $id  = $contentType === 'opinion' ? 3200 : 2200;
    $ads = getService('core.template')->getSmarty()
        ->tpl_vars['advertisements']->value;

    $adsInsideBody = array_filter($ads, function ($a) use ($id) {
        $type = (int) $a->type_advertisement;
        return $type > $id && $type < $id + 100;
    });

    $safeFrame = getService('core.helper.advertisement')->isSafeFrameEnabled();
    $renderer  = getService('core.renderer.advertisement');
    $html      = '<div class="ad-slot oat" data-type="%s"></div>';

    // Reset array keys and get total paragraphs
    $adsInsideBody   = array_values($adsInsideBody);
    $totalParagraphs = count($paragraphs);

    $usedSlots = [];
    foreach ($adsInsideBody as $key => $ad) {
        $slotId = $ad->type_advertisement;
        $ad     = sprintf($html, $slotId);
        $pos    = $slotId - $id;

        if (in_array($slotId, $usedSlots)) {
            continue;
        }

        if (!$safeFrame) {
            $adsForPosition = array_filter($adsInsideBody, function ($a) use ($slotId) {
                return (int) $a->type_advertisement == $slotId;
            });

            if (count($adsForPosition) < 1) {
                continue;
            }

            $ad = $adsForPosition[array_rand($adsForPosition)];
            $ad = $renderer->render($ad);
        }

        if ($pos <= $totalParagraphs) {
            array_splice($paragraphs, $pos, 0, $ad);
        } else {
            array_push($paragraphs, $ad);
        }

        array_push($usedSlots, $slotId);
    }

    return implode('', $paragraphs);
}
