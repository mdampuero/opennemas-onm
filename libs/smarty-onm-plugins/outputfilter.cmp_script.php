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

    $config = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get([ 'cookies', 'cmp_type', 'cmp_id', 'cmp_id_amp', 'cmp_apikey', 'marfeel_pass' ]);

    if (is_null($request) || $config['cookies'] !== 'cmp') {
        return $output;
    }

    $uri = $request->getUri();

    if (!preg_match('/newsletter/', $smarty->source->resource)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads\//', $uri)
        && !preg_match('/\/comments\//', $uri)
        && !preg_match('/\/hbbtv/', $uri)
        && !preg_match('/\/rss\/(?!listado$)/', $uri)
    ) {
        // Check for AMP pages
        if (preg_match('@\.amp\.html@', $uri)) {
            // Do nothing if CMP not fully configured
            if ($config['cmp_type'] === 'default'
                || ($config['cmp_type'] !== 'didomi' && empty($config['cmp_id']))
                || ($config['cmp_type'] === 'didomi' && (
                        empty($config['cmp_apikey']) || empty($config['cmp_id_amp'])
                    )
                )
            ) {
                return $output;
            }

            $ampId = $config['cmp_type'] === 'didomi' ? $config['cmp_id_amp'] : $config['cmp_id'];
            $code  = $smarty->getContainer()->get('core.template.admin')->fetch(
                'common/helpers/cmp_' . $config['cmp_type'] . '_amp.tpl',
                [
                    'apikey'    => $config['cmp_apikey'] ?? '',
                    'id'        => $ampId,
                    'mrfpassId' => $config['marfeel_pass']['id'] ?? ''
                ]
            );

            return preg_replace('@(<body.*?>)@', '${1}' . "\n" . $code, $output);
        }

        // Do nothing if CMP not fully configured
        if (($config['cmp_type'] !== 'default' && empty($config['cmp_id']))
            || ($config['cmp_type'] === 'didomi' && empty($config['cmp_apikey']))
        ) {
            return $output;
        }

        $code = $smarty->getContainer()->get('core.template.admin')->fetch(
            'common/helpers/cmp_' . $config['cmp_type'] . '.tpl',
            [
                'apikey'    => $config['cmp_apikey'] ?? '',
                'id'        => $config['cmp_id'],
                'mrfpassId' => $config['marfeel_pass']['id'] ?? '',
                'mrfpassCmp' => $config['marfeel_pass']['cmp'] ?? ''
            ]
        );

        $output = preg_replace('@(</head>)@', "\n" . $code . '${1}', $output);
    }

    return $output;
}
