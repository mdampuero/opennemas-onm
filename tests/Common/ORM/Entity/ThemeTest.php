<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Entity;

use Common\ORM\Entity\Theme;

class ThemeTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $data = [
            'parameters' => [
                'styles' => [
                    'default' => [
                        'default' => true,
                        'name'    => 'Default',
                        'params'  => [
                            'css_file' => 'style.css',
                        ]
                    ],
                    'style-two' => [
                        'name'   => 'Skin two',
                        'params' => [
                            'css_file' => 'style-two.css',
                        ]
                    ],
                ],
            ]
        ];

        $entity = new Theme($data);

        $this->assertEquals($entity->getData(), $data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $entity->{$key});
        }
    }

    public function testGetSkinsWithOneSkin()
    {
        $styles = [
            'default' => [
                'default'       => true,
                'name'          => 'Default',
                'internal_name' => 'default',
                'params'        => [
                    'css_file' => 'style.css',
                ]
            ],
            'style-two' => [
                'name'          => 'Skin two',
                'internal_name' => 'style-two',
                'params'        => [
                    'css_file' => 'style-two.css',
                ]
            ],
        ];

        $entity = new Theme([
            'parameters' => [
                'skins' => $styles,
            ]
        ]);

        $this->assertEquals($styles, $entity->getSkins());
    }

    public function testGetSkinsWithNoSkins()
    {
        $entity = new Theme([]);

        $this->assertEquals([], $entity->getSkins());
    }

    public function testGetDefaultSkinWithNoSkins()
    {
        $entity = new Theme([]);

        $this->assertEquals(null, $entity->getDefaultSkin());
    }

    public function testGetDefaultSkinWithSkins()
    {
        $styles = [
            'default' => [
                'default' => true,
                'name'   => 'Default',
                'params' => [
                    'css_file' => 'style.css',
                ]
            ],
            'style-two' => [
                'name'    => 'Skin two',
                'params'  => [
                    'css_file' => 'style-two.css',
                ]
            ],
        ];

        $entity = new Theme([
            'parameters' => [
                'skins' => $styles,
            ]
        ]);

        $this->assertEquals(
            [
                'internal_name' => 'default',
                'default' => true,
                'name'   => 'Default',
                'params' => [
                    'css_file' => 'style.css',
                ]
            ],
            $entity->getDefaultSkin()
        );
    }

    public function testGetDefaultSkinWithSkinsButNoDefault()
    {
        $styles = [
            'default' => [
                'name'   => 'Default',
                'params' => [
                    'css_file' => 'style.css',
                ]
            ],
            'style-two' => [
                'name'    => 'Skin two',
                'params'  => [
                    'css_file' => 'style-two.css',
                ]
            ],
        ];

        $entity = new Theme([
            'parameters' => [
                'skins' => $styles,
            ]
        ]);

        $this->assertEquals(
            [
                'internal_name' => 'default',
                'name'   => 'Default',
                'params' => [
                    'css_file' => 'style.css',
                ]
            ],
            $entity->getDefaultSkin()
        );
    }


    public function testGetCurrentSkinWithValidSelected()
    {
        $styles = [
            'default' => [
                'name'   => 'Default',
                'params' => [
                    'css_file' => 'style.css',
                ]
            ],
            'style-two' => [
                'name'    => 'Skin two',
                'params'  => [
                    'css_file' => 'style-two.css',
                ]
            ],
        ];

        $selected = 'default';

        $entity = new Theme([
            'parameters' => [
                'skins' => $styles,
            ]
        ]);

        $this->assertEquals(
            [
                'name'   => 'Default',
                'internal_name' => 'default',
                'params' => [
                    'css_file' => 'style.css',
                ]
            ],
            $entity->getCurrentSkin($selected)
        );
    }

    public function testGetCurrentSkinWithInValidSelected()
    {
        $styles = [
            'default' => [
                'default' => true,
                'name'    => 'Default',
                'params'  => [
                    'css_file' => 'style.css',
                ]
            ],
            'style-two' => [
                'name'    => 'Skin two',
                'params'  => [
                    'css_file' => 'style-two.css',
                ]
            ],
        ];

        $selected = 'style-not-valid';

        $entity = new Theme([
            'parameters' => [
                'skins' => $styles,
            ]
        ]);

        $this->assertEquals(
            [
                'default'       => true,
                'name'          => 'Default',
                'internal_name' => 'default',
                'params'        => [
                    'css_file' => 'style.css',
                ]
            ],
            $entity->getCurrentSkin($selected)
        );
    }

    public function testGetCurrentSkinWithInValidSelectedAndNoSkins()
    {
        $selected = 'style-not-valid';

        $entity = new Theme([
            'parameters' => []
        ]);

        $this->assertEquals(null, $entity->getCurrentSkin($selected));
    }
}
