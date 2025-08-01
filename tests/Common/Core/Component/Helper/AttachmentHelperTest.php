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

use Common\Core\Component\Helper\AttachmentHelper;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for AttachmentHelper class.
 */
class AttachmentHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'bar' ]);

        $this->il = $this->getMockBuilder('Common\Core\Component\Loader\InstanceLoader')
            ->disableOriginalConstructor()
            ->setMethods([ 'getInstance' ])
            ->getMock();

        $this->fs = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->disableOriginalConstructor()
            ->setMethods([ 'exists', 'remove' ])
            ->getMock();

        $this->il->expects($this->any())->method('getInstance')
            ->willReturn($this->instance);

        $this->helper = new AttachmentHelper($this->il, '/waldo/grault');

        $property = new \ReflectionProperty($this->helper, 'fs');
        $property->setAccessible(true);

        $property->setValue($this->helper, $this->fs);
    }

    /**
     * Tests exists.
     */
    public function testExists()
    {
        $this->fs->expects($this->at(0))->method('exists')
            ->with('/glork/quux.foo')->willReturn(true);
        $this->fs->expects($this->at(1))->method('exists')
            ->with('/foo/wobble.bar')->willReturn(false);

        $this->assertTrue($this->helper->exists('/glork/quux.foo'));
        $this->assertFalse($this->helper->exists('/foo/wobble.bar'));
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

        $file->expects($this->any())->method('getClientOriginalName')
            ->willReturn('xyzzy.gorp');

        $this->assertRegexp(
            '/\/waldo\/grault\/media\/bar\/files\/[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/xyzzy.gorp/',
            $this->helper->generatePath($file, new \DateTime())
        );

        $this->assertRegexp(
            '/\/waldo\/grault\/media\/bar\/files\/2010\/01\/01\/xyzzy.gorp/',
            $this->helper->generatePath($file, new \DateTime('2010-01-01 15:20:45'))
        );
    }

    /**
     * Tests getRelativePath.
     */
    public function testGetRelativePath()
    {
        $this->assertEquals(
            '2010/01/01/xyzzy.gorp',
            $this->helper->getRelativePath('/waldo/grault/media/bar/files/2010/01/01/xyzzy.gorp')
        );
    }

    /**
     * Tests move when the original file is copied to target.
     */
    public function testMove()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'move' ])
            ->getMock();

        $file->expects($this->once())->method('move')
            ->with('/thud/fred/', 'norf.txt');

        $this->helper->move($file, '/thud/fred/norf.txt');
    }
}
