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

    $html        = '<div class="ad-slot oat" data-type="%s"></div>';
    $bodyWithAds = [];

    foreach ($paragraphs as $key => $paragraph) {
        $slot   = '';
        $slotId = $id + 1 + $key;

        $bodyWithAds[] = $paragraph;
        $bodyWithAds[] = sprintf($html, $slotId);
    }

    return implode('', $bodyWithAds);
}
