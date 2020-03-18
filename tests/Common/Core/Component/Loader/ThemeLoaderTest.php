<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Loader;

use Common\Core\Component\Loader\ThemeLoader;
use Common\Model\Entity\Theme;

/**
 * Defines test cases for ThemeLoader class.
 */
class ThemeLoaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Common\ORM\File\Repository\BaseRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'findBy', 'findOneBy' ])
            ->getMock();

        $this->em->expects($this->any())->method('getRepository')
            ->with('theme', 'file')->willReturn($this->repository);

        $this->loader = new ThemeLoader($this->em);
    }

    /**
     * Tests getTheme when loader has and has not previously loaded the Theme.
     */
    public function testGetTheme()
    {
        $this->assertEmpty($this->loader->getTheme());

        $theme = new Theme([ 'uuid' => 'baz' ]);

        $property = new \ReflectionProperty($this->loader, 'theme');
        $property->setAccessible(true);
        $property->setValue($this->loader, $theme);

        $this->assertEquals($theme, $this->loader->getTheme());
    }

    /**
     * Test getThemeParents when theme has parents configured.
     */
    public function testGetThemeParents()
    {
        $this->assertEquals([], $this->loader->getThemeParents());

        $themes = [
            new Theme([ 'uuid' => 'es.openhost.theme.gorp' ]),
            new Theme([ 'uuid' => 'es.openhost.theme.foo' ])
        ];

        $property = new \ReflectionProperty($this->loader, 'parents');
        $property->setAccessible(true);
        $property->setValue($this->loader, $themes);

        $this->assertEquals($themes, $this->loader->getThemeParents());
    }

    /**
     * Tests loadThemeByUuid.
     */
    public function testLoadThemeByUuid()
    {
        $this->repository->expects($this->once())->method('findOneBy')
            ->with('uuid = "es.openhost.theme.mumble"');

        $this->loader->loadThemeByUuid('mumble');
    }

    /**
     * Test loadThemeParents when theme has no parents.
     */
    public function testLoadThemeParentsWhenNoParents()
    {
        $theme = new Theme();

        $property = new \ReflectionProperty($this->loader, 'theme');
        $property->setAccessible(true);
        $property->setValue($this->loader, $theme);

        $this->assertEquals($this->loader, $this->loader->loadThemeParents());
    }

    /**
     * Test loadThemeParents when theme has no parents.
     */
    public function testLoadThemeParentsWhenNoParets()
    {
        $theme = new Theme([ 'parameters' => [
            'parent' => [ 'es.openhost.theme.foo', 'es.openhost.theme.gorp' ]
        ] ]);

        $property = new \ReflectionProperty($this->loader, 'theme');
        $property->setAccessible(true);
        $property->setValue($this->loader, $theme);

        $themeA = new Theme([ 'uuid' => 'es.openhost.theme.gorp' ]);
        $themeB = new Theme([ 'uuid' => 'es.openhost.theme.foo' ]);

        $this->repository->expects($this->once())->method('findBy')
            ->with('uuid in ["es.openhost.theme.foo", "es.openhost.theme.gorp"]')
            ->willReturn([ $themeA, $themeB ]);

        $this->assertEquals($this->loader, $this->loader->loadThemeParents());
    }
}
