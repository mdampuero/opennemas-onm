<?php
/**
 * Generates all the script tags for OpenX based ads.
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_outputfilter_ads_generator($output, $smarty)
{
    $adsRenderer = $smarty->getContainer()->get('frontend.renderer.advertisement');
    $ads         = $adsRenderer->getRequestedAd();
    $app         = $smarty->getValue('app');

    if (!is_array($ads)
        || empty($ads)
        || preg_match('/newsletter/', $smarty->source->resource)
    ) {
        return $output;
    }

    // Do not render the ads headers for AMP pages.
    $adsFormat = $smarty->getValue('ads_format');
    if (in_array($adsFormat, $adsRenderer->getInlineFormats())) {
        return $output;
    }

    $content  = $smarty->getValue('content');
    $settings = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('ads_settings');

    $isSafeFrame = $smarty->getContainer()
        ->get('core.helper.advertisement')->isSafeFrameEnabled();

    $adsPositions  = $smarty->getValue('ads_positions') ?? [];
    $contentHelper = $smarty->getContainer()->get('core.helper.content');

    if (!$isSafeFrame) {
        $ads = array_filter($ads, function ($a) use ($contentHelper) {
            return $contentHelper->isInTime($a);
        });

        $params = [
            'category'           => $app['section'],
            'extension'          => $app['extension'],
            'advertisementGroup' => $app['advertisementGroup'],
            'content'            => $content,
            'x-tags'             => $smarty->getValue('x-tags'),
        ];

        $adsOutput    = $adsRenderer->renderInlineHeaders($ads, $params);
        $interstitial = $adsRenderer->renderInlineInterstitial($ads, $params);
        $devices      = $smarty->getContainer()->get('core.template.admin')
            ->fetch('advertisement/helpers/inline/js.tpl');

        $devices = "\n" . str_replace("\n", ' ', $devices);

        $output = str_replace('</head>', $adsOutput . '</head>', $output);
        $output = str_replace('</body>', $interstitial . '</body>', $output);
        $output = str_replace('</body>', $devices . '</body>', $output);
    }

    $content = $smarty->getContainer()->get('core.template.admin')
        ->fetch('advertisement/helpers/safeframe/js.tpl', [
            'debug'              => $app['environment'] === 'dev' ? 'true' : 'false',
            'category'           => $app['section'],
            'extension'          => $app['extension'],
            'advertisementGroup' => $app['advertisementGroup'],
            'contentId'          => $content->id ?? null,
            'lifetime'           => $settings['lifetime_cookie'],
            'positions'          => $isSafeFrame ? implode(',', $adsPositions) : '',
            'url'                => $smarty->getContainer()->get('router')
                ->generate('api_v1_advertisements_list')
        ]);

    $output = str_replace('</head>', $content . '</head>', $output);

    return $output;
}
