<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Cache\File;

use Common\Cache\File\File;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for File class.
 */
class FileTest extends KernelTestCase
{
    public function setUp()
    {
        $this->finder = $this->getMockBuilder('Finder')
            ->setMethods([ 'count', 'files', 'in', 'name' ])
            ->getMock();

        $this->fs = $this->getMockBuilder('Filesystem')
            ->setMethods([ 'dumpFile', 'mkdir', 'remove' ])
            ->getMock();

        $this->cache = $this->getMockBuilder('Common\Cache\File\File')
            ->setMethods([ 'getContent', 'getFinder', 'getFileSystem' ])
            ->setConstructorArgs([
                [
                    'name'      => 'bar',
                    'path'      => '/foo/bar',
                    'namespace' => 'frog'
                ]
            ])->getMock();

        $this->cache->expects($this->any())->method('getFinder')
            ->willReturn($this->finder);

        $this->cache->expects($this->any())->method('getFileSystem')
            ->willReturn($this->fs);
    }

    /**
     * Tests constructor with invalid redis parameters.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidConfiguration()
    {
        new File([]);
    }

    /**
     * Tests setNamespace and getNamespace.
     */
    public function testSetNamespace()
    {
        $this->cache->setNamespace('garply');
        $this->assertEquals('garply', $this->cache->getNamespace());
    }

    /**
     * Tests set and get with single values.
     */
    public function testWithSingleValues()
    {
        $object = json_decode(json_encode(['foo' => 'bar']));
        $file   = new \SplFileInfo('/foo/bar/frog_garply');

        $this->finder->expects($this->any())->method('name')->willReturn($this->finder);
        $this->finder->expects($this->at(1))->method('count')->willReturn(0);
        $this->finder->expects($this->at(2))->method('files')->willReturn([ $file ]);
        $this->finder->expects($this->at(3))->method('count')->willReturn(1);
        $this->finder->expects($this->at(5))->method('files')->willReturn([ $file ]);
        $this->finder->expects($this->at(7))->method('files')->willReturn([ $file ]);
        $this->finder->expects($this->at(9))->method('files')->willReturn([]);
        $this->finder->expects($this->at(11))->method('files')->willReturn([ $file ]);
        $this->finder->expects($this->at(13))->method('files')->willReturn([ $file ]);
        $this->finder->expects($this->at(15))->method('files')->willReturn([ $file ]);

        $this->fs->expects($this->at(0))->method('dumpFile')->with('/foo/bar/frog_garply', serialize('bar'));
        $this->fs->expects($this->at(1))->method('remove')->with('/foo/bar/frog_garply');
        $this->fs->expects($this->at(2))->method('dumpFile')->with('/foo/bar/frog_flob');
        $this->fs->expects($this->at(3))->method('remove')->with('/foo/bar/frog_garply');

        $this->cache->expects($this->at(4))->method('getContent')->with($file)->willReturn(serialize('bar'));
        $this->cache->expects($this->at(10))->method('getContent')->willReturn(serialize($object));

        $this->assertFalse($this->cache->exists('foo'));
        $this->cache->set('garply', 'bar', 60);
        $this->assertTrue($this->cache->exists('foo'));
        $this->assertEquals('bar', $this->cache->get('foo'));

        $this->cache->remove('garply');
        $this->assertEmpty($this->cache->get('garply'));

        $this->cache->set('flob', $object);
        $this->assertEquals($object, $this->cache->get('flob'));

        $this->cache->remove('flob');
        $this->assertEmpty($this->cache->get('flob'));
    }

    /**
     * Tests set and get with multiple keys and values.
     */
    public function testWithMultipleValues()
    {
        $file = new \SplFileInfo('/foo/bar/frog_garply');

        $this->finder->expects($this->any())->method('name')->willReturn($this->finder);

        $this->finder->expects($this->at(1))->method('count')->willReturn(1);
        $this->finder->expects($this->at(3))->method('count')->willReturn(1);
        $this->finder->expects($this->at(5))->method('files')->willReturn([ $file ]);
        $this->finder->expects($this->at(7))->method('files')->willReturn([ $file ]);
        $this->finder->expects($this->at(9))->method('files')->willReturn([ $file ]);
        $this->finder->expects($this->at(10))->method('count')->willReturn(1);
        $this->finder->expects($this->at(11))->method('files')->willReturn([ $file ]);

        $this->fs->expects($this->at(0))->method('dumpFile')->with('/foo/bar/frog_foo', serialize('bar'));
        $this->fs->expects($this->at(1))->method('dumpFile')->with('/foo/bar/frog_fred', serialize('wibble'));
        $this->fs->expects($this->at(2))->method('remove')->with('/foo/bar/frog_garply');

        $this->cache->expects($this->at(5))->method('getContent')->with($file)->willReturn(serialize('bar'));
        $this->cache->expects($this->at(7))->method('getContent')->with($file)->willReturn(serialize('wibble'));

        $this->cache->set([ 'foo' => 'bar', 'fred' => 'wibble' ]);

        $this->assertTrue($this->cache->exists('foo'));
        $this->assertTrue($this->cache->exists('fred'));

        $this->assertEquals(['foo' => 'bar', 'fred' => 'wibble' ], $this->cache->get([ 'foo', 'fred' ]));
        $this->assertEquals(['foo' => 'bar' ], $this->cache->get([ 'foo', 'garply' ]));

        $this->cache->remove([ 'foo' ]);
    }
}
