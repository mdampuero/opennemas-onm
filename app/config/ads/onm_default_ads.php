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
        'positions' => [ 1 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 3 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 5 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 11 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 12 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 21 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 22 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 31 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 32 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 33 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 34 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 35 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 91 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 92 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 101 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 103 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 104 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 105 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 106 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 107 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 108 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 109 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 191 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 192 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 193 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 601 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 603 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 605 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 609 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 701 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 703 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 704 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 705 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 709 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 791 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 792 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 793 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 1211 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 1212 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 1213 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 2201 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 3201 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 2203 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 3203 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 2205 ],
        'fk_content_categories' => [ 0 ],
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
        'positions' => [ 3205 ],
        'fk_content_categories' => [ 0 ],
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
