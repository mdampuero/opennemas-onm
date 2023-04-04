<?php
/**
 * Returns the favicon meta tag
 *
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_function_render_hreflang_tags($params, &$smarty)
{
    $instance = $smarty->getContainer()->get('core.instance');
    if (!$instance->hasMultilanguage()) {
        return;
    }

    $request = $smarty->getValue('app')->getRequest()->attributes;
    $context = $smarty->getContainer()->get('core.locale')->getContext();
    $uri     = $smarty->getContainer()->get('request_stack')->getCurrentRequest()->getRequestUri();

    $localeSettings = $smarty->getContainer()->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('locale');

    $mainLanguage = $localeSettings['frontend']['language']['selected'] ?? '';

    $smarty->getContainer()->get('core.locale')->setContext('frontend');
    $locale      = $smarty->getContainer()->get('core.locale');
    $currentSlug = $locale->getRequestSlug();
    $slugs       = $locale->getSlugs();

    if (empty($slugs)) {
        return;
    }

    if (strpos($uri, '/' . $currentSlug . '/') === 0) {
        $uri = str_replace('/' . $currentSlug, '', $uri);
    }

    $result  = '';
    $linkTpl = '<link rel="alternate" hreflang="%s" href="%s"/>' . "\n";

    $translatedParams = $smarty->getContainer()->get('core.helper.url_generator')
        ->translateUrlParams($request->get('_route_params'));

    foreach ($slugs as $longSlug => $shortSlug) {
        $filteredParams = array_map(function ($e) use ($longSlug) {
            return $e[$longSlug] ?? '';
        }, $translatedParams);

        $href = $instance->getBaseUrl();
        if ($locale->getLocale() !== $longSlug) {
            $href .= '/' . $shortSlug;
        }

        try {
            $url = $smarty->getContainer()->get('router')->generate(
                $request->get('_route'),
                $filteredParams
            );
        } catch (\Exception $e) {
            return;
        }

        $href   .= $url;
        $result .= sprintf($linkTpl, $shortSlug, $href);
    }

    $filteredParams = array_map(function ($e) use ($mainLanguage) {
        return $e[$mainLanguage] ?? '';
    }, $translatedParams);

    if (!empty($translatedParams)) {
        $uri = $smarty->getContainer()->get('router')->generate(
            $request->get('_route'),
            $filteredParams
        );
    }

    $result .= sprintf($linkTpl, 'x-default', $instance->getBaseUrl() . $uri);
    $locale->setContext($context);
    return $result;
}
