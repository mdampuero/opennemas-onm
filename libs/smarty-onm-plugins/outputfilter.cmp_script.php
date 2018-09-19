<?php
/**
 * Prints CMP code (Consent Management Platform)
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_outputfilter_cmp_script($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    if (empty($smarty->getContainer()->get('setting_repository')->get('cmp_script'))) {
        return $output;
    }

    $uri = $smarty->getContainer()->get('request_stack')->getCurrentRequest()->getUri();

    if (!preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/share-by-email/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads/', $uri)
        && !preg_match('/\/comments/', $uri)
        && !preg_match('/\/fb\/instant-articles/', $uri)
        && !preg_match('@\.amp\.html$@', $uri)
    ) {
        $code = $smarty->getContainer()->get('core.template.admin')->fetch(
            'common/helpers/cmp.tpl',
            [
                'lang' => $smarty->getContainer()->get('core.locale')->getLocaleShort(),
                'site' => $smarty->getContainer()->get('setting_repository')->get('site_name')
            ]
        );

        $output = preg_replace('@(</head>)@', "\n" . $code . '${1}', $output);
    }

    return $output;
}
