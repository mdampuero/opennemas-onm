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
                        'name' => 'Default',
                        'file' => 'style.css',
                    ],
                    'style-two' => [
                        'name' => 'Style two',
                        'file' => 'style-two.css',
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

    public function testGetStylesWithOneStyle()
    {
        $styles = [
            'default' => [
                'default' => true,
                'name' => 'Default',
                'file' => 'style.css',
            ],
            'style-two' => [
                'name' => 'Style two',
                'file' => 'style-two.css',
            ],
        ];

        $entity = new Theme([
            'parameters' => [
                'styles' => $styles,
            ]
        ]);

        $this->assertEquals($styles, $entity->getStyles());
    }

    public function testGetStylesWithNoStyles()
    {
        $entity = new Theme([]);

        $this->assertEquals([], $entity->getStyles());
    }

    public function testGetDefaultStyleWithNoStyles()
    {
        $entity = new Theme([]);

        $this->assertEquals(null, $entity->getDefaultStyle());
    }

    public function testGetDefaultStyleWithStyles()
    {
        $styles = [
            'default' => [
                'name' => 'Default',
                'file' => 'style.css',
            ],
            'style-two' => [
                'default' => true,
                'name' => 'Style two',
                'file' => 'style-two.css',
            ],
        ];

        $entity = new Theme([
            'parameters' => [
                'styles' => $styles,
            ]
        ]);

        $this->assertEquals(
            [
                'default' => true,
                'name' => 'Style two',
                'file' => 'style-two.css',
            ],
            $entity->getDefaultStyle()
        );
    }

    public function testGetDefaultStyleWithStylesButNoDefault()
    {
        $styles = [
            'default' => [
                'name' => 'Default',
                'file' => 'style.css',
            ],
            'style-two' => [
                'name' => 'Style two',
                'file' => 'style-two.css',
            ],
        ];

        $entity = new Theme([
            'parameters' => [
                'styles' => $styles,
            ]
        ]);

        $this->assertEquals(
            [
                'name' => 'Default',
                'file' => 'style.css',
            ],
            $entity->getDefaultStyle()
        );
    }


    public function testGetCurrentStyleWithValidSelected()
    {
        $styles = [
            'default' => [
                'name' => 'Default',
                'file' => 'style.css',
            ],
            'style-two' => [
                'name' => 'Style two',
                'file' => 'style-two.css',
            ],
        ];

        $selected = 'default';

        $entity = new Theme([
            'parameters' => [
                'styles' => $styles,
            ]
        ]);

        $this->assertEquals(
            [
                'name' => 'Default',
                'file' => 'style.css',
            ],
            $entity->getCurrentStyle($selected)
        );
    }

    public function testGetCurrentStyleWithInValidSelected()
    {
        $styles = [
            'default' => [
                'default' => true,
                'name'    => 'Default',
                'file'    => 'style.css',
            ],
            'style-two' => [
                'name' => 'Style two',
                'file' => 'style-two.css',
            ],
        ];

        $selected = 'style-not-valid';

        $entity = new Theme([
            'parameters' => [
                'styles' => $styles,
            ]
        ]);

        $this->assertEquals(
            [
                'default' => true,
                'name' => 'Default',
                'file' => 'style.css',
            ],
            $entity->getCurrentStyle($selected)
        );
    }

    public function testGetCurrentStyleWithInValidSelectedAndNoStyles()
    {
        $selected = 'style-not-valid';

        $entity = new Theme([
            'parameters' => []
        ]);

        $this->assertEquals(null, $entity->getCurrentStyle($selected));
    }
}
