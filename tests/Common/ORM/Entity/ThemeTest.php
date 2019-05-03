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

use \Common\ORM\Entity\Theme;

class ThemeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests constructor.
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
     * Tests getSkins when theme has no skins.
     */
    public function testGetSkinsWhenNoSkins()
    {
        $theme = new Theme([]);

        $this->assertEmpty($theme->getSkins());
    }

    /**
     * Tests getSkins when theme has skins.
     */
    public function testGetSkinsWhenSkins()
    {
        $theme = new Theme([
            'parameters' => [
                'skins' => [
                    'default' => [
                        'default' => true,
                        'name'    => 'Default'
                    ]
                ]
            ]
        ]);

        $this->assertEquals([ 'default' => [
            'default'       => true,
            'internal_name' => 'default',
            'name'          => 'Default',
        ] ], $theme->getSkins());
    }

    /**
     * Tests getSkin when the theme has no skins defined.
     */
    public function testGetSkinWhenNoSkins()
    {
        $theme = new Theme([ 'parameters' => [] ]);

        $this->assertEmpty($theme->getSkin('fred'));
    }

    /**
     * Tests getSkin when the theme has skins for valid and invalid ids.
     */
    public function testGetSkinWhenSkins()
    {
        $theme = new Theme([
            'parameters' => [
                'skins' => [
                    'default' => [
                        'name'   => 'Default',
                        'params' => [
                            'css_file' => 'style.css',
                        ]
                    ]
                ]
            ]
        ]);

        $this->assertEmpty($theme->getSkin('plugh'));
        $this->assertEquals([
            'name'          => 'Default',
            'internal_name' => 'default',
            'params'        => [ 'css_file' => 'style.css', ]
        ], $theme->getSkin('default'));
    }

    /**
     * Tests getSkinProperty when the theme has no skins.
     */
    public function testGetSkinPropertyWhenNoSkins()
    {
        $theme = new Theme([ 'parameters' => [] ]);

        $this->assertEmpty($theme->getSkinProperty('fred', 'css_file'));
    }

    /**
     * Tests getSkinProperty when theme has skins for valid and invalid skins
     * and properties.
     */
    public function testGetSkinPropertyWhenSkins()
    {
        $theme = new Theme([
            'parameters' => [
                'skins' => [
                    'valid' => [
                        'default' => true,
                        'name'    => 'Valid skin',
                        'params'  => [
                            'css_file' => 'style.css',
                        ]
                    ],
                    'incomplete' => [
                        'name'    => 'Incomplete skin',
                        'params'  => []
                    ]
                ]
            ]
        ]);

        $this->assertEmpty($theme->getSkinProperty('incomplete', 'css_file'));
        $this->assertEmpty($theme->getSkinProperty('valid', 'baz'));
        $this->assertEquals('style.css', $theme->getSkinProperty('valid', 'css_file'));
    }

    /**
     * Tests getDefaultSkin when no skins defined.
     */
    public function testGetDefaultSkinWithNoSkins()
    {
        $theme  = new Theme([]);
        $method = new \ReflectionMethod($theme, 'getDefaultSkin');

        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($theme, []));
    }

    /**
     * Tests getDefaultSkin when skins
     */
    public function testGetDefaultSkinWhenNoDefaultSkin()
    {
        $theme = new Theme([
            'parameters' => [
                'skins' => [
                    'default' => [
                        'name'   => 'Default',
                        'params' => [
                            'css_file' => 'style.css',
                        ]
                    ]
                ]
            ]
        ]);

        $method = new \ReflectionMethod($theme, 'getDefaultSkin');

        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($theme, []));
    }

    /**
     * Tests getDefaultSkin when skins
     */
    public function testGetDefaultSkinWhenDefaultSkin()
    {
        $theme = new Theme([
            'parameters' => [
                'skins' => [
                    'default' => [
                        'default' => true,
                        'name'    => 'Default',
                        'params'  => [ 'css_file' => 'style.css' ]
                    ]
                ]
            ]
        ]);

        $method = new \ReflectionMethod($theme, 'getDefaultSkin');

        $method->setAccessible(true);

        $this->assertEquals([
            'default'       => true,
            'internal_name' => 'default',
            'name'          => 'Default',
            'params'        => [ 'css_file' => 'style.css' ]
        ], $method->invokeArgs($theme, []));
    }
}
