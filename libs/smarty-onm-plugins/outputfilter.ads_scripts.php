<?php
/**
 * Prints scripts from instance settings
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_outputfilter_ads_scripts($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getUri();

    if (!preg_match('/newsletter/', $smarty->source->resource)
        && !preg_match('/\/admin\/frontpages/', $uri)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads\//', $uri)
        && !preg_match('/\/comments\//', $uri)
        && !preg_match('/\/rss\/(?!listado$)/', $uri)
    ) {
        $headerScript    = 'header_script';
        $bodyStartScript = 'body_start_script';
        $bodyEndScript   = 'body_end_script';
        $customCssAmp    = '';

        if (preg_match('@\.amp\.html@', $uri)) {
            $headerScript    .= '_amp';
            $bodyStartScript .= '_amp';
            $bodyEndScript   .= '_amp';
            $customCssAmp     = 'custom_css_amp';
        }

        $settings = $smarty->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get([ $headerScript, $bodyStartScript, $bodyEndScript, $customCssAmp ]);

        if (array_key_exists($headerScript, $settings)
            && !empty($settings[$headerScript])
        ) {
            $output = preg_replace(
                '@(</head>)@',
                "\n" . base64_decode($settings[$headerScript]) . "\n" . '${1}',
                $output
            );
        }

        if (array_key_exists($bodyStartScript, $settings)
            && !empty($settings[$bodyStartScript])
        ) {
            $output = preg_replace(
                '@(<body.*>)@',
                '${1}' . "\n" . base64_decode($settings[$bodyStartScript]) . "\n",
                $output
            );
        }

        if (array_key_exists($bodyEndScript, $settings)
            && !empty($settings[$bodyEndScript])
        ) {
            $output = preg_replace(
                '@(</body.*>)@',
                "\n" . base64_decode($settings[$bodyEndScript]) . "\n" . '${1}',
                $output
            );
        }

        if (!empty($customCssAmp)
            && array_key_exists($customCssAmp, $settings)
            && !empty($settings[$customCssAmp])
        ) {
            $output = preg_replace(
                '@(<style amp-custom>(?s).*?)(</style>)@',
                '${1}' . "\n" . base64_decode($settings[$customCssAmp]) . "\n" . '${2}',
                $output
            );
        }
    }

    return $output;
}
