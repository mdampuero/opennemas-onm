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

    // L10n for urls
    $requestedLocale = $smarty->getContainer()
        ->get('request_stack')->getCurrentRequest()
        ->attributes->get('_locale', '');

    // If no locale skip the l10n setting
    if (empty($requestedLocale)) {
        return $url;
    }

    $localeSettings = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('locale');

    $localeForUri = '';
    if (is_array($localeSettings)
        && is_array($localeSettings)
        && array_key_exists('main', $localeSettings)
        && $requestedLocale !== $localeSettings['main']
        && in_array($requestedLocale, $localeSettings['frontend'])
    ) {
        $localeForUri = $requestedLocale;
    }

    // Get the list of routes that could be localized
    $routes = array_filter(
        $smarty->getContainer()->get('router')->getRouteCollection()->all(),
        function ($route) {
            // return !$route->hasOption('l10n');
            return true === $route->getOption('l10n')
                || 'true' === $route->getOption('l10n');
        }
    );
    $routes = array_keys($routes);

    // Only localize urls if the user comes from a localized site
    // and if the url can be localized
    if (!empty($localeForUri)
        && in_array($name, $routes)
    ) {
        // Append the locale for uri to the url path part
        if ($forceAbsolute) {
            $parts         = parse_url($url);
            $parts['path'] = $localeForUri . $parts['path'];
            $url           = implode('/', $parts);

            return $url;
        }

        $url = '/' . $localeForUri . $url;
    }

    return $url;
}
