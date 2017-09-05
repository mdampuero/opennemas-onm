<?php
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    if ($forceAbsolute) {
        $absolute = UrlGeneratorInterface::ABSOLUTE_URL;
    } else {
        $absolute = UrlGeneratorInterface::ABSOLUTE_PATH;
    }

    unset($params['name'], $params['absolute']);
    try {
        $url = $smarty->getContainer()
            ->get('router')
            ->generate($name, $params, $absolute);
    } catch (RouteNotFoundException $e) {
        $url = '#not-found-' . $params['name'];
    } catch (RouteNotFoundException $e) {
        $url = '#not-found-' . $params['name'];
    } catch (\Exception $e) {
        $url = '#not-found';
    }

    // L10n for urls
    $requestedLocale = $smarty->getContainer()
        ->get('request_stack')->getCurrentRequest()
        ->attributes->get('_locale', '');

    // If no locale the skip the l10n setting
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

    // List of excluded url names from l10n
    $excludedUrlFromL10n = [
        'frontend_css_global',
        'frontend_css_frontpage_category',
        'frontend_ad_get',
        'frontend_ad_redirect',
        'frontend_auth_login',
        'frontend_auth_check',
        'frontend_auth_logout',
        'frontend_comments_get',
        'frontend_comments_ajax',
        'frontend_comments_vote',
        'frontend_comments_save',
        'frontend_comments_count',
        'frontend_content_stats',
        'frontend_content_share_by_mail',
        'frontend_content_permalink',
        'frontend_letter_save',
        'frontend_paywall_showcase',
        'frontend_poll_vote',
        'frontend_redirect_content',
        'frontend_redirect_category',
        'frontend_rtb_file',
        'frontend_rss_author',
        'frontend_ws_paypal_ipn'
    ];

    if (!empty($localeForUri) // Only localize if locale is defined
        && strpos($name, 'frontend') === 0 // Only localize for frontend urls
        && !in_array($name, $excludedUrlFromL10n) // Exclude some url names
    ) {
        // Append the locale for uri to the url path part
        if ($forceAbsolute) {
            $parts         = parse_url($url);
            $parts['path'] = $localeForUri . $parts['path'];
            $url           = implode('/', $parts);
        } else {
            $url = '/' . $localeForUri . $url;
        }
    }

    return $url;
}
