<?php
// Onm advertisement php file
$default = [
    'with_script' => '1',
    'content_type' => '2',
    'content_type_name' => 'advertisement',
    'starttime' => '2011-09-28 23:44:24',
    'endtime' => null,
    'created' => '2011-09-28 23:44:24',
    'changed' => '2011-09-28 23:44:24',
    'available' => '1',
    'content_status' => '1',
    'content_type_l10n_name' => 'Publicidad',
    'fk_content_type' => '2',
    'params' => [
        'orientation' => 'top',
        'devices' => [
            'desktop' => 1,
            'tablet' => 1,
            'phone' => 1
        ],
        'restriction_devices' => [
            'phone' => 1,
            'tablet' => 1,
            'desktop' => 1
        ]
    ],
];

// Pre head tag smart
$preScript = '<script src="//ced.sascdn.com/tag/3035/smart.js" async></script>
<script>
    var sas = sas || {};
    sas.cmd = sas.cmd || [];
    sas.cmd.push(function() {
        sas.setup({ networkid: 3035, domain: "//www8.smartadserver.com", async: true });
    });
</script>';

$i = 1;
return array_map(function ($ad) use (&$i, $default) {
    $adObject = new Advertisement();
    // Merge default params on all ads
    $ad = array_merge($default, $ad);

    foreach ($ad as $key => $value) {
        $adObject->{$key} = $value;
    }

    $adObject->id               = $i;
    $adObject->pk_advertisement = $i;
    $adObject->pk_content       = $i;
    $adObject->pk_fk_content    = $i;
    $adObject->default_ad       = 1;

    $i++;

    return $adObject;
}, [
    1 => [
        'type_advertisement' => '1',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64035"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64035, // Format : megabanner_header 980x250
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 980,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 980,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 320,
                    'height' => 100,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    3 => [
        'type_advertisement' => '3',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64119"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64119, // Format : megabanner_middle 970x250
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 320,
                    'height' => 100,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    5 => [
        'type_advertisement' => '5',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64112"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64112, // Format : megabanner_footer 970x250
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 320,
                    'height' => 100,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    11 => [
        'type_advertisement' => '11',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64438"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64438, // Format : rectangle_1.1 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    12 => [
        'type_advertisement' => '12',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64439"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64439, // Format : rectangle_1.2 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
            'devices' => [
                'desktop' => 0,
                'tablet' => 0,
                'phone' => 1
            ],
        ],
    ],
    21 => [
        'type_advertisement' => '21',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64442"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64442, // Format : rectangle_2.1 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
            'devices' => [
                'desktop' => 0,
                'tablet' => 0,
                'phone' => 1
            ],
        ],
    ],
    22 => [
        'type_advertisement' => '22',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64443"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64443, // Format : rectangle_2.2 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    31 => [
        'type_advertisement' => '31',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64036"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64036, // Format : rectangle_3.1 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    32 => [
        'type_advertisement' => '32',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64113"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64113, // Format : rectangle_3.2 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    33 => [
        'type_advertisement' => '33',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64114"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64114, // Format : rectangle_3.3 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    34 => [
        'type_advertisement' => '34',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64115"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64115, // Format : rectangle_3.4 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    35 => [
        'type_advertisement' => '35',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64116"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64116, // Format : rectangle_3.5 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    91 => [
        'type_advertisement' => '91',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64109"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64109, // Format : Skyscraper_left 160x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 160,
                    'height' => 600,
                    'device' => 'desktop'
                ],
            ],
            'devices' => [
                'desktop' => 1,
                'tablet' => 0,
                'phone' => 0
            ],
        ],
    ],
    92 => [
        'type_advertisement' => '92',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64108"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64108, // Format : Skyscraper_right 160x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 160,
                    'height' => 600,
                    'device' => 'desktop'
                ],
            ],
            'devices' => [
                'desktop' => 1,
                'tablet' => 0,
                'phone' => 0
            ],
        ],
    ],
    101 => [
        'type_advertisement' => '101',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64035"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64035, // Format : megabanner_header 980x250
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 980,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 980,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 980,
                    'height' => 250,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    103 => [
        'type_advertisement' => '103',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64036"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64036, // Format : rectangle_3.1 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    104 => [
        'type_advertisement' => '104',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64572"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64572, // Format : banner_under_body 728x550
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 728,
                    'height' => 550,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 728,
                    'height' => 550,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 320,
                    'height' => 100,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    105 => [
        'type_advertisement' => '105',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64113"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64113, // Format : rectangle_3.2 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    106 => [
        'type_advertisement' => '106',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64114"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64114, // Format : rectangle_3.3 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    107 => [
        'type_advertisement' => '107',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64115"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64115, // Format : rectangle_3.4 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    108 => [
        'type_advertisement' => '108',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64116"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64116, // Format : rectangle_3.5 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    109 => [
        'type_advertisement' => '109',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64112"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64112, // Format : megabanner_footer 970x250
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    191 => [
        'type_advertisement' => '191',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64109"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64109, // Format : Skyscraper_left 160x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 160,
                    'height' => 600,
                    'device' => 'desktop'
                ],
            ],
            'devices' => [
                'desktop' => 1,
                'tablet' => 0,
                'phone' => 0
            ],
        ],
    ],
    192 => [
        'type_advertisement' => '192',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64108"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64108, // Format : Skyscraper_right 160x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 160,
                    'height' => 600,
                    'device' => 'desktop'
                ],
            ],
            'devices' => [
                'desktop' => 1,
                'tablet' => 0,
                'phone' => 0
            ],
        ],
    ],
    193 => [
        'type_advertisement' => '193',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64110"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64110, // Format : Skyscraper_inbody 120x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 120,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 120,
                    'height' => 600,
                    'device' => 'tablet'
                ],
            ],
            'devices' => [
                'desktop' => 1,
                'tablet' => 1,
                'phone' => 0
            ],
        ],
    ],
    601 => [
        'type_advertisement' => '601',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64035"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64035, // Format : megabanner_header 980x250
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 980,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 980,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 980,
                    'height' => 250,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    603 => [
        'type_advertisement' => '603',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64036"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64036, // Format : rectangle_3.1 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    605 => [
        'type_advertisement' => '605',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64113"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64113, // Format : rectangle_3.2 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    609 => [
        'type_advertisement' => '609',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64112"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64112, // Format : megabanner_footer 970x250
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    701 => [
        'type_advertisement' => '701',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64035"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64035, // Format : megabanner_header 980x250
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 980,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 980,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 980,
                    'height' => 250,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    703 => [
        'type_advertisement' => '703',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64036"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64036, // Format : rectangle_3.1 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    704 => [
        'type_advertisement' => '704',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64572"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64572, // Format : banner_under_body 728x550
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 728,
                    'height' => 550,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 728,
                    'height' => 550,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 320,
                    'height' => 100,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    705 => [
        'type_advertisement' => '705',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64113"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64113, // Format : rectangle_3.2 300x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    709 => [
        'type_advertisement' => '709',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64112"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64112, // Format : megabanner_footer 970x250
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 970,
                    'height' => 250,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    791 => [
        'type_advertisement' => '791',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64109"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64109, // Format : Skyscraper_left 160x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 160,
                    'height' => 600,
                    'device' => 'desktop'
                ],
            ],
            'devices' => [
                'desktop' => 1,
                'tablet' => 0,
                'phone' => 0
            ],
        ],
    ],
    792 => [
        'type_advertisement' => '792',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64108"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64108, // Format : Skyscraper_right 160x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 160,
                    'height' => 600,
                    'device' => 'desktop'
                ],
            ],
            'devices' => [
                'desktop' => 1,
                'tablet' => 0,
                'phone' => 0
            ],
        ],
    ],
    793 => [
        'type_advertisement' => '793',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64110"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64110, // Format : Skyscraper_inbody 120x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 120,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 120,
                    'height' => 600,
                    'device' => 'tablet'
                ],
            ],
            'devices' => [
                'desktop' => 1,
                'tablet' => 1,
                'phone' => 0
            ],
        ],
    ],
    1211 => [
        'type_advertisement' => '1211',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64435"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64435, // Format : horizontal_banner_1 728x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 728,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 728,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 320,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    1212 => [
        'type_advertisement' => '1212',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64436"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64436, // Format : horizontal_banner_2 728x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 728,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 728,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 320,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    1213 => [
        'type_advertisement' => '1213',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64437"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64437, // Format : horizontal_banner_3 728x600
                    });
                });
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 728,
                    'height' => 600,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 728,
                    'height' => 600,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 320,
                    'height' => 600,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    2201 => [
        'type_advertisement' => '2201',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64120"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64120, // Format : banner_intext_1 600x300
                    });
                });
            </script>',
    ],
    3201 => [
        'type_advertisement' => '3201',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64120"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64120, // Format : banner_intext_1 600x300
                    });
                });
            </script>',
    ],
    2203 => [
        'type_advertisement' => '2203',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64457"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64457, // Format : Intext_video 1x1
                    });
                });
            </script>',
    ],
    3203 => [
        'type_advertisement' => '3203',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64457"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64457, // Format : Intext_video 1x1
                    });
                });
            </script>',
    ],
    2205 => [
        'type_advertisement' => '2205',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64121"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64121, // Format : banner_intext_2 600x300
                    });
                });
            </script>',
    ],
    3205 => [
        'type_advertisement' => '3205',
        'fk_content_categories' => [ '0' ],
        'script' => $preScript . '<div id="sas_64121"></div>
            <script type="application/javascript">
                sas.cmd.push(function() {
                    sas.call("std", {
                        siteId: 214597, //
                        pageId: 905245, // Page : Opennemas/global
                        formatId: 64121, // Format : banner_intext_2 600x300
                    });
                });
            </script>',
    ],
]);
