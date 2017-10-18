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

    $name          = $params['name'];
    $forceAbsolute = array_key_exists('absolute', $params) && $params['absolute'];
    $absoluteUrl   = $forceAbsolute
        ? UrlGeneratorInterface::ABSOLUTE_URL
        : UrlGeneratorInterface::ABSOLUTE_PATH;

    unset($params['name'], $params['absolute']);
    try {
        $url = $smarty->getContainer()
            ->get('router')
            ->generate($name, $params, $absoluteUrl);
    } catch (RouteNotFoundException $e) {
        $url = '#not-found-' . $name;
    } catch (\Exception $e) {
        $url = '#not-found';
    }

    $url = $smarty->getContainer()->get('core.helper.l10n_route')->localizeUrl($url, $name, $forceAbsolute);

    return $url;
}
