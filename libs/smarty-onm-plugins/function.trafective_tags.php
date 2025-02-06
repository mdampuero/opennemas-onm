<?php

function smarty_function_trafective_tags($params, &$smarty)
{
    $output = [];

    $ds = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance');

    $content = $smarty->getValue('content');

    $data = [
        'category' => (null !== $smarty->getValue('o_category'))
                      ? $smarty->getValue('o_category')->getData()['name'] ?? 'homepage'
                      : 'homepage',
        'url'      => $smarty->getContainer()->get('request_stack')
            ->getCurrentRequest()->getUri(),
    ];

    $output[] = '<script type="text/javascript"
        id="trfAdSetup"
        async
        data-traffectiveConf=\'{
            "targeting": [
                {"key":"zone","values":null,"value":"' . $data['category'] . '"},
                {"key":"pagetype","values":null,"value":"' . 'default_pagetype' . '"},
                {"key":"programmatic_ads","values":null,"value":"' . 'true' . '"},
                {"key":"ads","values":null,"value":"' . 'true' . '"}
            ]
        }\'>
    </script>';


    return implode("\n", $output);
}
