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
    $smarty       = getService('core.template');
    $request      = $smarty->getContainer()->get('request_stack')->getCurrentRequest();
    $isRestricted = !empty($request)
        ? $smarty->getContainer()->get('core.helper.advertisement')->isRestricted($request->getUri())
        : false;

    if ($isRestricted) {
        return $body;
    }

    // Split body into paragraphs and avoid losing data at body end
    preg_match_all('/(.*?)<\/p>(\s<\/blockquote>)?/s', $body . '<p></p>', $matches);

    // Clean empty paragraphs
    $paragraphs = array_filter($matches[0], function ($a) {
        return !in_array(trim($a), ['<p>&nbsp;</p>', '<p></p>']);
    });

    $id       = $contentType === 'amp' ? 1060 : ($contentType === 'opinion' ? 3200 : 2200);
    $renderer = $smarty->getContainer()->get('frontend.renderer.advertisement');
    $ads      = $renderer->getAdvertisements();
    $hasLimit = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('ads_settings')['limit_ads_in_body'];

    if (empty($ads)) {
        return $body;
    }

    // Get targeting parameters for smart ajax format
    $app    = $smarty->getValue('app');
    $params = [
        'category'           => $app['section'],
        'extension'          => $app['extension'],
        'advertisementGroup' => $app['advertisementGroup'],
        'content'            => $smarty->getValue('content'),
        'ads_format'         => $contentType === 'amp' ? $contentType : null,
    ];

    $slots = [];
    foreach ($ads as $ad) {
        $slots = array_merge($slots, $ad->positions);
    }

    // Limit ads to the paragraphs number
    $limitSlots = $hasLimit ? count($paragraphs) : 10;
    $slots      = array_unique(array_filter($slots, function ($a) use ($id, $limitSlots) {
        return $a > $id && $a < $id + $limitSlots;
    }));

    sort($slots);

    $html      = '<div class="ad-slot oat" data-type="%s"></div>';
    $device    = $smarty->getContainer()->get('core.globals')->getDevice();
    $safeFrame = $smarty->getContainer()->get('core.helper.advertisement')
        ->isSafeFrameEnabled();

    // Use skip to avoid advertisements that are not selected for the current device
    $skip = 0;
    foreach ($slots as $key => $slotId) {
        $ad  = sprintf($html, $slotId);
        $pos = $slotId - $id;

        if (!$safeFrame || $contentType === 'amp') {
            $adsForPosition = array_filter($ads, function ($a) use ($slotId, $device) {
                return in_array($slotId, $a->positions)
                    && ($a->params['devices'][$device] === 1
                        || empty($device)
                    );
            });

            if (empty($adsForPosition)) {
                $skip = $skip + 1;
                continue;
            }

            $ad = $adsForPosition[array_rand($adsForPosition)];
            $ad = $renderer->render($ad, $params);
        }

        array_splice($paragraphs, $pos + $key - $skip, 0, $ad);
    }

    return implode('', $paragraphs);
}
