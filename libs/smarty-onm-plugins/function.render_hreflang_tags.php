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
    $request  = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (!$instance->hasMultilanguage() || empty($request)) {
        return;
    }

    $localeSettings = $smarty->getContainer()->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('locale');

    $mainLanguage = $localeSettings['frontend']['language']['selected'] ?? '';
    $locale       = $smarty->getContainer()->get('core.locale');
    $currentSlug  = $locale->getRequestSlug();
    $slugs        = $locale->getSlugs();
    $uri          = $request->getRequestUri();

    if (empty($slugs)) {
        return;
    }

    if (strpos($uri, '/' . $currentSlug . '/') === 0) {
        $uri = str_replace('/' . $currentSlug, '', $uri);
    }

    $result  = '';
    $linkTpl = '<link rel="alternate" hreflang="%s" href="%s"/>' . "\n";

    $translatedParams = $smarty->getContainer()->get('core.helper.url_generator')
        ->getTranslatedUrlParams($request->get('_route_params'));

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

    return $result;
}
