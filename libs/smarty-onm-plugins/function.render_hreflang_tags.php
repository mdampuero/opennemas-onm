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

    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();
    $context = $smarty->getContainer()->get('core.locale')->getContext();
    $uri     = $request->getRequestUri();

    $smarty->getContainer()->get('core.locale')->setContext('frontend');

    $locale      = $smarty->getContainer()->get('core.locale');
    $slugs       = $locale->getSlugs();
    $currentSlug = $locale->getRequestSlug();

    if (strpos($uri, '/' . $currentSlug . '/') === 0) {
        $uri = str_replace('/' . $currentSlug, '', $uri);
    }

    $result  = '';
    $linkTpl = '<link rel="alternate" hreflang="%s" href="%s"/>' . "\n";

    foreach ($slugs as $longSlug => $shortSlug) {
        $href = $instance->getBaseUrl();
        if ($locale->getLocale() !== $longSlug) {
            $href .= '/' . $shortSlug;
        }

        $href   .= $uri;
        $result .= sprintf($linkTpl, strtolower(str_replace('_', '-', $longSlug)), $href);
    }

    $result .= sprintf($linkTpl, 'x-default', $instance->getBaseUrl() . $uri);
    return $result;
}
