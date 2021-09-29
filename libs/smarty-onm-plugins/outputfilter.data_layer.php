<?php
/**
 * Prints Google Analytics code
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_data_layer($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getUri();

    if (!preg_match('/newsletter/', $smarty->source->resource)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads\//', $uri)
        && !preg_match('/\/comments\//', $uri)
        && !preg_match('/\/rss\/(?!listado$)/', $uri)
        && !preg_match('@\.amp\.html@', $uri)
    ) {
        $dataLayerMap = $smarty->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('data_layer');

        if (empty($dataLayerMap)) {
            return $output;
        }

        $code = $smarty->getContainer()->get('core.service.data_layer')
            ->getDataLayerCode($dataLayerMap);

        $output = preg_replace('@(</head>)@', $code . '${1}', $output);
    }

    return $output;
}
