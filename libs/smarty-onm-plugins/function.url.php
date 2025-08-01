<?php
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
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

    $name     = $params['name'];
    $absolute = $params['absolute'] ?? null;

    unset($params['name'], $params['absolute'], $params['sluggable'], $params['slug_key']);

    // TODO: Refactor the code to don't use this array.
    $esi = [ 'frontend_suggested_show', 'frontend_date_render', 'frontend_widget_render', 'frontend_inline_styles' ];

    try {
        foreach ($params as $key => $value) {
            if (is_null($value) && !in_array($name, $esi)) {
                throw new InvalidParameterException(
                    sprintf('Parameter "%s" for route "%s" is empty.', $key, $name)
                );
            }
        }

        $path = $smarty->getContainer()
            ->get('router')
            ->generate($name, $params);

        $url = $absolute
            ? $smarty->getContainer()->get('core.instance')->getBaseUrl() . $path
            : $path;
    } catch (RouteNotFoundException $e) {
        $url = '#not-found-' . $name;
    } catch (InvalidParameterException $e) {
        $url = '#not-found-invalid-parameter';
    } catch (\Exception $e) {
        $url = '#not-found';
    }

    $url = is_string($url) ?
        $smarty->getContainer()->get('core.decorator.url')->prefixUrl($url) :
        $url;

    return $url;
}
