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
    $request     = $smarty->getContainer()->get('request_stack')->getCurrentRequest();
    $adsRenderer = $smarty->getContainer()->get('frontend.renderer.advertisement');
    $isSafeFrame = $smarty->getContainer()->get('core.helper.advertisement')->isSafeFrameEnabled();
    $ads         = $isSafeFrame ? $adsRenderer->getAdvertisements() : $adsRenderer->getRequested();
    $app         = $smarty->getValue('app');
    $expiringAds = $adsRenderer->getExpiringAdvertisements();
    if (!is_array($ads)
        || (empty($ads) && empty($expiringAds))
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

    if (!$isSafeFrame) {
        $params = [
            'category'           => $app['section'],
            'extension'          => $app['extension'],
            'advertisementGroup' => $app['advertisementGroup'],
            'content'            => $content,
            'x-tags'             => $smarty->getValue('x-tags'),
        ];

        if (!empty($expiringAds)) {
            $headers         = $request->headers->all();
            $currentLifeTime = $adsRenderer->getXCacheFor($expiringAds);
            if (!array_key_exists('x-cache-for', $headers)) {
                header('x-cache-for: ' . $currentLifeTime, true);
            } elseif (array_key_exists('x-cache-for', $headers) &&
            (substr($currentLifeTime, 0, -1) < substr($headers['x-cache-for'], 0, -1))) {
                header('x-cache-for: ' . $currentLifeTime, true);
            }
        }
        $adsOutput    = $adsRenderer->renderInlineHeaders($params);
        $interstitial = $adsRenderer->renderInlineInterstitial($params);
        $devices      = $smarty->getContainer()->get('core.template.admin')
            ->fetch('advertisement/helpers/inline/js.tpl');

        $devices = "\n" . str_replace("\n", ' ', $devices);

        $output = str_replace('</head>', $adsOutput . '</head>', $output);
        $output = str_replace('</body>', $interstitial . '</body>', $output);
        $output = str_replace('</body>', $devices . '</body>', $output);
    }

    $adsPositions = $adsRenderer->getPositions();

    $url = $smarty->getContainer()->get('router')
        ->generate('api_v1_advertisements_list');

    $url = is_string($url)
        ? $smarty->getContainer()->get('core.decorator.url')->prefixUrl($url)
        : $url;

    $content = $smarty->getContainer()->get('core.template.admin')
        ->fetch('advertisement/helpers/safeframe/js.tpl', [
            'debug'              => $app['environment'] === 'dev' ? 'true' : 'false',
            'category'           => $app['section'],
            'extension'          => $app['extension'],
            'advertisementGroup' => $app['advertisementGroup'],
            'contentId'          => $content->id ?? null,
            'lifetime'           => $settings['lifetime_cookie'],
            'positions'          => $isSafeFrame ? implode(',', $adsPositions) : '',
            'url'                => $url
        ]);

    $output = str_replace('</head>', $content . '</head>', $output);

    return $output;
}
