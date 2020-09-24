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
    $request = $smarty->getContainer()
        ->get('request_stack')
        ->getCurrentRequest();

    $ds = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance');

    if (is_null($request) || $ds->get('cookies') !== 'cmp') {
        return $output;
    }

    $uri = $request->getUri();

    if (!preg_match('/newsletter/', $smarty->source->resource)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/share-by-email/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads/', $uri)
        && !preg_match('/\/comments/', $uri)
        && !preg_match('/\/rss/', $uri)
        && !preg_match('@\.amp\.html$@', $uri)
    ) {
        $code = $smarty->getContainer()->get('core.template.admin')->fetch(
            'common/helpers/cmp_' . $ds->get('cmp_type') . '.tpl',
            [ 'id' => $ds->get('cmp_id') ]
        );

        $output = preg_replace('@(</head>)@', "\n" . $code . '${1}', $output);
    }

    return $output;
}
