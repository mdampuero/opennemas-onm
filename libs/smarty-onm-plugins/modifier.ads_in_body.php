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
    // Split body into paragraphs and avoid losing data at body end
    preg_match_all('/(.*?)<\/p>(\s<\/blockquote>)?/s', $body . '<p></p>', $matches);

    // Clean empty paragraphs
    $paragraphs = array_filter($matches[0], function ($a) {
        return !in_array(trim($a), ['<p>&nbsp;</p>', '<p></p>']);
    });

    $id     = $contentType === 'opinion' ? 3200 : 2200;
    $smarty = getService('core.template');
    $ads    = $smarty->getValue('advertisements');

    if (empty($ads)) {
        return $body;
    }

    // Get targeting parameters for smart ajax format
    $app    = $smarty->getValue('app');
    $params = [
        'category'           => $app['section'],
        'extension'          => $app['extension'],
        'advertisementGroup' => $app['advertisementGroup'],
        'content'            => $smarty->getValue('content')
    ];

    $slots = [];
    foreach ($ads as $ad) {
        $slots = array_merge($slots, $ad->positions);
    }

    $slots = array_unique(array_filter($slots, function ($a) use ($id) {
        return $a > $id && $a < $id + 100;
    }));

    sort($slots);

    $safeFrame = getService('core.helper.advertisement')->isSafeFrameEnabled();
    $renderer  = getService('core.renderer.advertisement');
    $html      = '<div class="ad-slot oat" data-type="%s"></div>';

    foreach ($slots as $key => $slotId) {
        $ad  = sprintf($html, $slotId);
        $pos = $slotId - $id;

        if (!$safeFrame) {
            $adsForPosition = array_filter($ads, function ($a) use ($slotId) {
                return in_array($slotId, $a->positions);
            });

            $ad = $adsForPosition[array_rand($adsForPosition)];
            $ad = $renderer->render($ad, $params);
        }

        array_splice($paragraphs, $pos + $key, 0, $ad);
    }

    return implode('', $paragraphs);
}
