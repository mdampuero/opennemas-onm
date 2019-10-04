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
    $ads = $smarty->getValue('advertisements');
    $app = $smarty->getValue('app');

    if (!is_array($ads)
        || empty($ads)
        || preg_match('/newsletter/', $smarty->source->resource)
    ) {
        return $output;
    }

    // Do not render the ads headers for AMP pages.
    $formatFromTemplateVar = (
            array_key_exists('render_params', $smarty->tpl_vars)
            && array_key_exists('ads-format', $smarty->tpl_vars['render_params']->value)
            && $smarty->tpl_vars['render_params']->value['ads-format']
        ) ? $smarty->tpl_vars['render_params']->value['ads-format']
        : null;

    if ($formatFromTemplateVar == 'amp') {
        return $output;
    }

    $content = $smarty->hasValue('content')
        ? $smarty->getValue('content')
        : null;

    $positions = [];
    $settings  = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('ads_settings');

    $safeFrameEnabled = $smarty->getContainer()
        ->get('core.helper.advertisement')->isSafeFrameEnabled();

    if (!$safeFrameEnabled) {
        $adsRenderer = $smarty->getContainer()->get('frontend.renderer.advertisement');
        $ads         = array_filter($ads, function ($a) {
            return $a->isInTime();
        });

        $params = [
            'category'           => $app['section'],
            'extension'          => $app['extension'],
            'advertisementGroup' => $app['advertisementGroup'],
            'content'            => $content,
            'x-tags'             => $smarty->smarty->tpl_vars['x-tags']->value,
        ];

        $adsOutput    = $adsRenderer->renderInlineHeaders($ads, $params);
        $interstitial = $adsRenderer->renderInlineInterstitial($ads, $params);
        $devices      = getService('core.template.admin')
            ->fetch('advertisement/helpers/inline/js.tpl');

        $devices = "\n" . str_replace("\n", ' ', $devices);

        $output = str_replace('</head>', $adsOutput . '</head>', $output);
        $output = str_replace('</body>', $interstitial . '</body>', $output);
        $output = str_replace('</body>', $devices . '</body>', $output);
    } else {
        // No advertisements
        if (!array_key_exists('ads_positions', $smarty->parent->tpl_vars)
            || !is_array($smarty->parent->tpl_vars['ads_positions']->value)
        ) {
            return $output;
        }

        $positions = is_object($smarty->parent->tpl_vars['ads_positions']) ?
            $smarty->parent->tpl_vars['ads_positions']->value : [];
    }

    $content = getService('core.template.admin')
        ->fetch('advertisement/helpers/safeframe/js.tpl', [
            'debug'              => $app['environment'] === 'dev' ? 'true' : 'false',
            'category'           => $app['section'],
            'extension'          => $app['extension'],
            'advertisementGroup' => $app['advertisementGroup'],
            'contentId'          => $content->id ?? null,
            'lifetime'           => $settings['lifetime_cookie'],
            'positions'          => implode(',', $positions),
            'url'                => getService('router')
                ->generate('api_v1_advertisements_list')
        ]);

    $output = str_replace('</head>', $content . '</head>', $output);

    return $output;
}
