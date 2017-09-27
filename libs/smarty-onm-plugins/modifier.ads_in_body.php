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
    preg_match_all('/(.*?)<\/p>/s', $body . '<p></p>', $matches);

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

    $slots = array_map(function ($a) {
        return (int) $a->type_advertisement;
    }, $ads);

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
                return (int) $a->type_advertisement == $slotId;
            });

            if (count($adsForPosition) < 1) {
                continue;
            }

            $ad = $adsForPosition[array_rand($adsForPosition)];
            $ad = $renderer->render($ad);
        }

        array_splice($paragraphs, $pos + $key, 0, $ad);
    }

    return implode('', $paragraphs);
}
