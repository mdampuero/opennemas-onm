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
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm-728x90 Leaderboard -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:728px;height:90px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="2721775077"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 728,
                    'height' => 90,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 728,
                    'height' => 90,
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
    '1m' => [
        'type_advertisement' => '1',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 320x100 Large mobile -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:320px;height:100px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="4186659580"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 320,
                    'height' => 100,
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
    2 => [
        'type_advertisement' => '2',
        'fk_content_categories' => [ '0' ],
        'img' => '126',
        'path' => '126',
        'url' => 'http://www.opennemas.com',
        'script' => '<a target="_blank" href="https://www.opennemas.com/es/'
            . 'registro?utm_source=Opennemas_free&utm_medium=banner&utm_term=free_newspapers'
            . '&utm_content=234x90&utm_campaign=periodico_gratuito" rel="nofollow">'
            . '<img alt="" src="/assets/images/advertisement/static/onm_ad234x90.jpg" width="234" height="90"></a>',
        'with_script' => '0',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 234,
                    'height' => 90,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 234,
                    'height' => 90,
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
    3 => [
        'type_advertisement' => '3',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 970x250 Billboard -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:970px;height:250px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="5734231891"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
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
            ],
            'devices' => [
                'desktop' => 1,
                'tablet' => 1,
                'phone' => 0
            ],
        ],
    ],
    '3m' => [
        'type_advertisement' => '3',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-format="fluid"
                 data-ad-layout="image-top"
                 data-ad-layout-key="-8i+1w-dq+e9+ft"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="9036619446"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'devices' => [
                'desktop' => 0,
                'tablet' => 0,
                'phone' => 1
            ],
        ],
    ],
    5 => [
        'type_advertisement' => '5',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm-728x90 Leaderboard -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:728px;height:90px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="2721775077"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 728,
                    'height' => 90,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 728,
                    'height' => 90,
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
    6 => [
        'type_advertisement' => '6',
        'fk_content_categories' => [ '0' ],
        'script' => '<a target="_blank" href="https://www.opennemas.com/es/'
            . 'registro?utm_source=Opennemas_free&utm_medium=banner&utm_term=free_newspapers'
            . '&utm_content=234x90&utm_campaign=periodico_gratuito" rel="nofollow">'
            . '<img alt="" src="/assets/images/advertisement/static/onm_ad234x90.jpg" width="234" height="90"></a>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 234,
                    'height' => 90,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 234,
                    'height' => 90,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 234,
                    'height' => 90,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    11 => [
        'type_advertisement' => '11',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 300x600 Large Skyscraper -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:300px;height:600px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="4024853965"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
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
    21 => [
        'type_advertisement' => '21',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 320x100 Large mobile -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:320px;height:100px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="4186659580"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 320,
                    'height' => 100,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 320,
                    'height' => 100,
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
    31 => [
        'type_advertisement' => '31',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm-300x250 - Medium Rectangle - #1 -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:300px;height:250px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="9055006270"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 250,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    101 => [
        'type_advertisement' => '101',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 970x250 Billboard -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:970px;height:250px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="5734231891"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
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
            ],
            'devices' => [
                'desktop' => 1,
                'tablet' => 1,
                'phone' => 0
            ],
        ],
    ],
    '101m' => [
        'type_advertisement' => '101',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 320x100 Large mobile -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:320px;height:100px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="4186659580"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 320,
                    'height' => 100,
                    'device' => 'desktop'
                ],
            ],
            'devices' => [
                'desktop' => 0,
                'tablet' => 0,
                'phone' => 1
            ],
        ],
    ],
    103 => [
        'type_advertisement' => '103',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 300x600 Large Skyscraper -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:300px;height:600px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="4024853965"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
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
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm-468x60-robapagina -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:468px;height:60px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="7755935294"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 468,
                    'height' => 60,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 468,
                    'height' => 60,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 468,
                    'height' => 60,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    105 => [
        'type_advertisement' => '105',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm-300x250 - Medium Rectangle - #1 -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:300px;height:250px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="9055006270"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 250,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    109 => [
        'type_advertisement' => '109',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm-728x90 Leaderboard -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:728px;height:90px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="2721775077"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 728,
                    'height' => 90,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 728,
                    'height' => 90,
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
    110 => [
        'type_advertisement' => '110',
        'fk_content_categories' => [ '0' ],
        'img' => '126',
        'path' => '126',
        'url' => 'http://www.opennemas.com',
        'script' => '<a target="_blank" href="https://www.opennemas.com/es/'
            . 'registro?utm_source=Opennemas_free&utm_medium=banner&utm_term=free_newspapers'
            . '&utm_content=234x90&utm_campaign=periodico_gratuito" rel="nofollow">'
            . '<img alt="" src="/assets/images/advertisement/static/onm_ad234x90.jpg" width="234" height="90"></a>',
        'with_script' => '0',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 234,
                    'height' => 90,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 234,
                    'height' => 90,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 234,
                    'height' => 90,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    193 => [
        'type_advertisement' => '193',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm-120x600-Skycraper -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:120px;height:600px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="2407405696"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
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
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm-728x90 Leaderboard -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:728px;height:90px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="2721775077"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 728,
                    'height' => 90,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 728,
                    'height' => 90,
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
    '601m' => [
        'type_advertisement' => '601',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 320x100 Large mobile -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:320px;height:100px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="4186659580"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 320,
                    'height' => 100,
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
    602 => [
        'type_advertisement' => '602',
        'fk_content_categories' => [ '0' ],
        'img' => '126',
        'path' => '126',
        'url' => 'http://www.opennemas.com',
        'script' => '<a target="_blank" href="https://www.opennemas.com/es/'
            . 'registro?utm_source=Opennemas_free&utm_medium=banner&utm_term=free_newspapers'
            . '&utm_content=234x90&utm_campaign=periodico_gratuito" rel="nofollow">'
            . '<img alt="" src="/assets/images/advertisement/static/onm_ad234x90.jpg" width="234" height="90"></a>',
        'with_script' => '0',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 234,
                    'height' => 90,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 234,
                    'height' => 90,
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
    603 => [
        'type_advertisement' => '603',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 300x600 Large Skyscraper -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:300px;height:600px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="4024853965"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
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
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm-300x250 - Medium Rectangle - #1 -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:300px;height:250px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="9055006270"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 250,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    609 => [
        'type_advertisement' => '609',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 970x250 Billboard -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:970px;height:250px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="5734231891"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
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
            ],
            'devices' => [
                'desktop' => 1,
                'tablet' => 1,
                'phone' => 0
            ],
        ],
    ],
    701 => [
        'type_advertisement' => '701',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 970x250 Billboard -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:970px;height:250px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="5734231891"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
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
            ],
            'devices' => [
                'desktop' => 1,
                'tablet' => 1,
                'phone' => 0
            ],
        ],
    ],
    '701m' => [
        'type_advertisement' => '701',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 320x100 Large mobile -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:320px;height:100px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="4186659580"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 320,
                    'height' => 100,
                    'device' => 'desktop'
                ],
            ],
            'devices' => [
                'desktop' => 0,
                'tablet' => 0,
                'phone' => 1
            ],
        ],
    ],
    703 => [
        'type_advertisement' => '703',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm - 300x600 Large Skyscraper -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:300px;height:600px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="4024853965"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
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
    705 => [
        'type_advertisement' => '705',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm-300x250 - Medium Rectangle - #1 -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:300px;height:250px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="9055006270"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 300,
                    'height' => 250,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 300,
                    'height' => 250,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 300,
                    'height' => 250,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    793 => [
        'type_advertisement' => '793',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm-120x600-Skycraper -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:120px;height:600px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="2407405696"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
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
    704 => [
        'type_advertisement' => '704',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Onm-468x60-robapagina -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:468px;height:60px"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="7755935294"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
        'params' => [
            'sizes' => [
                '0' => [
                    'width' => 468,
                    'height' => 60,
                    'device' => 'desktop'
                ],
                '1' => [
                    'width' => 468,
                    'height' => 60,
                    'device' => 'tablet'
                ],
                '2' => [
                    'width' => 468,
                    'height' => 60,
                    'device' => 'phone'
                ],
            ],
        ],
    ],
    2202 => [
        'type_advertisement' => '2202',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <ins class="adsbygoogle"
                 style="display:block; text-align:center;"
                 data-ad-format="fluid"
                 data-ad-layout="in-article"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="8755647912"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
    ],
    3202 => [
        'type_advertisement' => '3202',
        'fk_content_categories' => [ '0' ],
        'script' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <ins class="adsbygoogle"
                 style="display:block; text-align:center;"
                 data-ad-format="fluid"
                 data-ad-layout="in-article"
                 data-ad-client="ca-pub-7694073983816204"
                 data-ad-slot="8755647912"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>',
    ],
]);
