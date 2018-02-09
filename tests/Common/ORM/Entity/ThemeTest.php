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
    /**
     * @covers Common\ORM\Entity\Theme::__construct
     */
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

    /**
     * @covers Common\ORM\Entity\Theme::getSkins
     */
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

    /**
     * @covers Common\ORM\Entity\Theme::getSkins
     */
    public function testGetSkinsWithNoSkins()
    {
        $entity = new Theme([]);

        $this->assertEquals([], $entity->getSkins());
    }

    /**
     * @covers Common\ORM\Entity\Theme::getSkins
     */
    public function testGetDefaultSkinWithNoSkins()
    {
        $entity = new Theme([]);

        $this->assertEquals(null, $entity->getDefaultSkin());
    }

    /**
     * @covers Common\ORM\Entity\Theme::getDefaultSkin
     */
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

    /**
     * @covers Common\ORM\Entity\Theme::getDefaultSkin
     */
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

    /**
     * @covers Common\ORM\Entity\Theme::getDefaultSkin
     */
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

    /**
     * @covers Common\ORM\Entity\Theme::getCurrentSkin
     */
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

    /**
     * @covers Common\ORM\Entity\Theme::getCurrentSkin
     */
    public function testGetCurrentSkinWithInValidSelectedAndNoSkins()
    {
        $selected = 'style-not-valid';

        $entity = new Theme([
            'parameters' => []
        ]);

        $this->assertEquals(null, $entity->getCurrentSkin($selected));
    }

    /**
     * @covers Common\ORM\Entity\Theme::getCurrentSkinName
     */
    public function testgetCurrentSkinNameWithInValidSelectedAndNoSkins()
    {
        $selected = 'style-not-valid';

        $entity = new Theme([
            'parameters' => []
        ]);

        $this->assertEquals(null, $entity->getCurrentSkinName($selected));
    }

    /**
     * @covers Common\ORM\Entity\Theme::getCurrentSkinName
     */
    public function testgetCurrentSkinNameWithValidSelected()
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

        $selected = 'default';

        $entity = new Theme([
            'parameters' => [
                'skins' => $styles,
            ]
        ]);

        $this->assertEquals('default', $entity->getCurrentSkinName($selected));
    }

    /**
     * @covers Common\ORM\Entity\Theme::getCurrentSkinProperty
     */
    public function testgetCurrentSkinPropertyWithInValidSelectedAndNoSkins()
    {
        $selected = 'style-not-valid';

        $entity = new Theme([
            'parameters' => []
        ]);

        $this->assertEquals(null, $entity->getCurrentSkinProperty($selected, 'css_file'));
    }

    /**
     * @covers Common\ORM\Entity\Theme::getCurrentSkinProperty
     */
    public function testgetCurrentSkinPropertyWithValidSelected()
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

        $selected = 'default';

        $entity = new Theme([
            'parameters' => [
                'skins' => $styles,
            ]
        ]);

        $this->assertEquals('style.css', $entity->getCurrentSkinProperty($selected, 'css_file'));

        $selected = 'default';

        $entity = new Theme([
            'parameters' => [
                'skins' => [
                    'default' => [
                    ]
                ]
            ]
        ]);

        $this->assertEquals(null, $entity->getCurrentSkinProperty($selected, 'css_file'));

        $selected = 'default';

        $entity = new Theme([
            'parameters' => [
                'skins' => [
                    'default' => [
                        'params' => [
                        ]
                    ]
                ]
            ]
        ]);

        $this->assertEquals(null, $entity->getCurrentSkinProperty($selected, 'css_file'));

        $selected = 'default';

        $entity = new Theme([
            'parameters' => [
                'skins' => [
                    'default' => [
                        'params' => null
                    ]
                ]
            ]
        ]);

        $this->assertEquals(null, $entity->getCurrentSkinProperty($selected, 'css_file'));
    }
}
