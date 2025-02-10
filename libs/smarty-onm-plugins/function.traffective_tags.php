<?php

function smarty_function_traffective_tags($params, &$smarty)
{
    $output = [];

    $ds = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance');

    $trafectiveEnabled = $ds->get('traffective') ?? 0;

    if (!$trafectiveEnabled) {
        return null;
    }

    $data = [
        'category' => (null !== $smarty->getValue('o_category'))
                      ? $smarty->getValue('o_category')->getData()['name'] ?? 'homepage'
                      : 'homepage',
        'pagetype' => $smarty->getContainer()->get('core.globals')->getExtension(),
        'url'      => $smarty->getContainer()->get('request_stack')
            ->getCurrentRequest()->getUri(),
    ];

    $output[] = '<script type="text/javascript"
        id="trfAdSetup"
        async
        data-traffectiveConf=\'{
            "targeting": [
                {"key":"zone","values":null,"value":"' . $data['category'] . '"},
                {"key":"pagetype","values":null,"value":"' . $data['pagetype'] . '"},
                {"key":"programmatic_ads","values":null,"value":"' . 'true' . '"},
                {"key":"ads","values":null,"value":"' . 'true' . '"}
            ]
        }\'>
    </script>';


    return implode("\n", $output);
}
