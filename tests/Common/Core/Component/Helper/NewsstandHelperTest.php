<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\NewsstandHelper;
use Common\ORM\Entity\Instance;

/**
 * Defines test cases for NewsstandHelper class.
 */
class NewsstandHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'bar' ]);

        $this->fs = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->disableOriginalConstructor()
            ->setMethods([ 'exists', 'remove' ])
            ->getMock();

        $this->helper = new NewsstandHelper($this->instance, '/waldo/grault');

        $property = new \ReflectionProperty($this->helper, 'fs');
        $property->setAccessible(true);

        $property->setValue($this->helper, $this->fs);
    }

    /**
     * Tests generatePath.
     */
    public function testGeneratePath()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClientOriginalName' ])
            ->getMock();

        $this->assertRegexp(
            '/\/waldo\/grault\/media\/bar\/kiosko\/[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/[0-9]{19}.pdf/',
            $this->helper->generatePath($file, new \DateTime())
        );

        $this->assertRegexp(
            '/\/waldo\/grault\/media\/bar\/kiosko\/2010\/01\/01\/20100101152045[0-9]{5}.pdf/',
            $this->helper->generatePath($file, new \DateTime('2010-01-01 15:20:45'))
        );
    }

    /**
     * Tests getRelativePath.
     */
    public function testGetPathForFile()
    {
        $method = new \ReflectionMethod($this->helper, 'getPathForFile');
        $method->setAccessible(true);

        $this->assertEquals(
            '/media/bar/kiosko',
            $method->invokeArgs($this->helper, [])
        );
    }
}
