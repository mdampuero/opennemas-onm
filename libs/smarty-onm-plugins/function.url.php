<?php
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/*
 * Returns the url given a set of params
 *
 * @param array $params the list of smarty paramters
 * @param Smarty $smarty the smarty object
 *
 * @return string the url for the given parameters, empty if not valid
 */
function smarty_function_url($params, &$smarty)
{
    $url = '';
    if (!array_key_exists('name', $params)) {
        return $url;
    }

    // Hack for opinions authors frontpage url's
    if (array_key_exists('sluggable', $params) &&
        array_key_exists('slug_key', $params) &&
        array_key_exists($params['slug_key'], $params) &&
        $params['sluggable']
    ) {
        $params[$params['slug_key']] =
            \Onm\StringUtils::generateSlug($params[$params['slug_key']]);
    }

    $name          = $params['name'];
    $forceAbsolute = array_key_exists('absolute', $params) && $params['absolute'];
    $absoluteUrl   = $forceAbsolute
        ? UrlGeneratorInterface::ABSOLUTE_URL
        : UrlGeneratorInterface::ABSOLUTE_PATH;

    unset($params['name'], $params['absolute'], $params['sluggable'], $params['slug_key']);

    try {
        $url = $smarty->getContainer()
            ->get('router')
            ->generate($name, $params, $absoluteUrl);
    } catch (RouteNotFoundException $e) {
        $url = '#not-found-' . $name;
    } catch (\Exception $e) {
        $url = '#not-found';
    }

    $url = $smarty->getContainer()->get('core.helper.l10n_route')
        ->localizeUrl($url, $name);

    return $url;
}
